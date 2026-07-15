#include <WiFi.h>
#include <HTTPClient.h>
#include <ArduinoJson.h>
#include <Wire.h>
#include <LiquidCrystal.h>
#include "MAX30105.h"
#include <math.h>

//======================================================
// WIFI CREDENTIALS
//======================================================

const char* ssid     = "YOUR_WIFI_NAME";
const char* password  = "YOUR_WIFI_PASSWORD";

//======================================================
// SERVER ENDPOINT
//======================================================

const char* serverUrl = "http://192.168.1.100:8000/api/sensor-data";

//======================================================
// SEND TIMER
//======================================================

unsigned long lastSend    = 0;
const long    sendInterval = 5000;  // 5 seconds

//======================================================
// LCD PINS
//======================================================

LiquidCrystal lcd(14, 27, 26, 25, 33, 32);

//======================================================
// MAX30105 (SEN-16474)
//======================================================

MAX30105 particleSensor;

//======================================================
// SENSOR PINS
//======================================================

#define MQ2_PIN   34
#define MQ5_PIN   35

#define HUM_PIN   36      // VP
#define TEMP_PIN  39      // VN

//======================================================
// ADC SETTINGS
//======================================================

#define ADC_REF 3.3
#define ADC_MAX 4095.0

//======================================================
// MQ Thresholds
//======================================================

#define MQ2_LOW      350
#define MQ2_MEDIUM   800
#define MQ2_HIGH     1500

#define MQ5_LOW      800
#define MQ5_MEDIUM   2000
#define MQ5_HIGH     2500

//======================================================
// GLOBAL VARIABLES
//======================================================

int mq2Value = 0;
int mq5Value = 0;

int humRaw = 0;
int tempRaw = 0;

float humidity = 0;
float temperature = 0;

long dustIR = 0;

float estimatedAQI = 0;

String gasStatus = "";
String airStatus = "";

//======================================================
// ADC Averaging
//======================================================

int readAverage(int pin)
{
    long total = 0;

    for(int i=0;i<10;i++)
    {
        total += analogRead(pin);
        delay(2);
    }

    return total/10;
}

//======================================================
// Temperature
//======================================================

float calcTemperature(int raw)
{
    float voltage = raw * (ADC_REF / ADC_MAX);

    float temp =
        5.26 * pow(voltage,3)
      -27.34 * pow(voltage,2)
      +68.87 * voltage
      -29.0;

    // Temporary calibration
    temp -= 60;

    return temp;
}

//======================================================
// Humidity
//======================================================

float calcHumidity(int raw)
{
    float voltage = raw * (ADC_REF / ADC_MAX);

    float hum =
      ((voltage-0.8)/(2.7-0.8))*100.0;

    hum = constrain(hum,0,100);

    return hum;
}

//======================================================
// Estimated AQI
//======================================================

float calculateAQI(int mq2,int mq5)
{
    float aqi1 = (mq2/ADC_MAX)*500.0;
    float aqi2 = (mq5/ADC_MAX)*500.0;

    return (aqi1+aqi2)/2.0;
}

//======================================================
// Air Quality Status
//======================================================

String getAQIStatus(float aqi)
{
    if(aqi<=50)
        return "Good";

    if(aqi<=100)
        return "Moderate";

    if(aqi<=150)
        return "Unhealthy";

    if(aqi<=200)
        return "Very Bad";

    return "Hazardous";
}

//======================================================
// Advanced Gas Detection
//======================================================

String detectGasLevel(int mq2, int mq5)
{
    if (mq2 < 300 && mq5 < 700)
        return "SAFE";

    else if (mq2 < 500 && mq5 < 1800)
        return "SAFE";

    else if (mq2 < 900 && mq5 < 2200)
        return "MEDIUM";

    else if (mq2 < 1500 && mq5 < 3000)
        return "HIGH";

    else
        return "DANGER";
}

//======================================================
// Map gas status to API format (SAFE / DANGER)
//======================================================

String getApiGasStatus(String status)
{
    if (status == "SAFE" || status == "LOW")
        return "SAFE";

    return "DANGER";
}

//======================================================
// Map air status to API format
//======================================================

String getApiAirStatus(String status)
{
    if (status == "Very Bad")
        return "Unhealthy";

    return status;
}

//======================================================
// Read All Sensors
//======================================================

void readSensors()
{
    mq2Value = readAverage(MQ2_PIN);
    mq5Value = readAverage(MQ5_PIN);
    humRaw   = readAverage(HUM_PIN);
    tempRaw  = readAverage(TEMP_PIN);

    humidity      = calcHumidity(humRaw);
    temperature   = calcTemperature(tempRaw);
    dustIR        = particleSensor.getIR();
    estimatedAQI  = calculateAQI(mq2Value, mq5Value);
    gasStatus     = detectGasLevel(mq2Value, mq5Value);
    airStatus     = getAQIStatus(estimatedAQI);
}

//======================================================
// SEND DATA TO LARAVEL API
//======================================================

void sendToServer()
{
    if (WiFi.status() != WL_CONNECTED)
    {
        Serial.println("[WiFi] Not connected. Skipping send.");
        return;
    }

    // Build JSON
    StaticJsonDocument<512> doc;
    doc["temperature"]   = round(temperature * 10.0) / 10.0;
    doc["humidity"]      = round(humidity * 10.0) / 10.0;
    doc["mq2"]           = mq2Value;
    doc["mq5"]           = mq5Value;
    doc["dust"]          = (int)dustIR;
    doc["estimated_aqi"] = (int)estimatedAQI;
    doc["gas_status"]    = getApiGasStatus(gasStatus);
    doc["air_status"]    = getApiAirStatus(airStatus);

    String payload;
    serializeJson(doc, payload);

    Serial.println("[API] Sending payload:");
    Serial.println(payload);

    // HTTP POST
    HTTPClient http;
    http.begin(serverUrl);
    http.addHeader("Content-Type", "application/json");
    http.setTimeout(5000);

    int httpResponseCode = http.POST(payload);

    if (httpResponseCode > 0)
    {
        Serial.print("[API] Response code: ");
        Serial.println(httpResponseCode);

        if (httpResponseCode == 201)
            Serial.println("[API] Data stored successfully!");
        else
            Serial.println("[API] Unexpected response.");
    }
    else
    {
        Serial.print("[API] Error: ");
        Serial.println(http.errorToString(httpResponseCode));
    }

    http.end();
}

//======================================================
// SETUP
//======================================================

void setup()
{
    Serial.begin(115200);

    //----------------------------------------------
    // LCD Initialize
    //----------------------------------------------
    lcd.begin(16,2);
    lcd.clear();

    lcd.setCursor(0,0);
    lcd.print(" Smart Air ");
    lcd.setCursor(0,1);
    lcd.print(" Monitoring ");
    delay(2500);

    //----------------------------------------------
    // WiFi Connect
    //----------------------------------------------
    lcd.clear();
    lcd.setCursor(0,0);
    lcd.print("Connecting");
    lcd.setCursor(0,1);
    lcd.print("WiFi...");

    Serial.println("[WiFi] Connecting to ");
    Serial.println(ssid);

    WiFi.begin(ssid, password);

    int wifiTimeout = 0;
    while (WiFi.status() != WL_CONNECTED)
    {
        delay(500);
        Serial.print(".");
        wifiTimeout++;

        // Timeout after 20 seconds
        if (wifiTimeout > 40)
        {
            Serial.println();
            Serial.println("[WiFi] Connection FAILED!");
            lcd.clear();
            lcd.setCursor(0,0);
            lcd.print("WiFi Failed!");
            lcd.setCursor(0,1);
            lcd.print("Check Credentials");
            delay(3000);
            break;
        }
    }

    if (WiFi.status() == WL_CONNECTED)
    {
        Serial.println();
        Serial.print("[WiFi] Connected! IP: ");
        Serial.println(WiFi.localIP());

        lcd.clear();
        lcd.setCursor(0,0);
        lcd.print("WiFi Connected");
        lcd.setCursor(0,1);
        lcd.print(WiFi.localIP());
        delay(2500);
    }

    //----------------------------------------------
    // I2C Start
    //----------------------------------------------
    lcd.clear();
    lcd.setCursor(0,0);
    lcd.print("Initializing");
    lcd.setCursor(0,1);
    lcd.print("Sensors...");
    delay(2000);

    Wire.begin();

    //----------------------------------------------
    // Dust Sensor Initialization
    //----------------------------------------------
    if(!particleSensor.begin(Wire,I2C_SPEED_FAST))
    {
        Serial.println("MAX30105 NOT FOUND!");

        lcd.clear();
        lcd.setCursor(0,0);
        lcd.print("Dust Sensor");
        lcd.setCursor(0,1);
        lcd.print("Not Found!");

        while(1);
    }

    particleSensor.setup();
    Serial.println("Dust Sensor Ready");

    //----------------------------------------------
    // MQ Sensor Warmup
    //----------------------------------------------
    lcd.clear();
    lcd.setCursor(0,0);
    lcd.print("MQ Warmup");

    for(int i=5;i>=1;i--)
    {
        lcd.setCursor(0,1);
        lcd.print("Wait ");
        lcd.print(i);
        lcd.print(" sec   ");

        Serial.print("MQ Warmup : ");
        Serial.println(i);

        delay(1000);
    }

    lcd.clear();
    lcd.setCursor(0,0);
    lcd.print("Reading");
    lcd.setCursor(0,1);
    lcd.print("Baseline...");
    delay(2000);

    //----------------------------------------------
    // Read initial values
    //----------------------------------------------
    for(int i=0;i<20;i++)
    {
        readSensors();
        delay(100);
    }

    Serial.println("--------------------------------");
    Serial.println("System Started Successfully");
    Serial.println("--------------------------------");

    lcd.clear();
    lcd.setCursor(0,0);
    lcd.print("System Ready");
    lcd.setCursor(0,1);
    lcd.print("Monitoring...");
    delay(2500);

    lcd.clear();
}

//======================================================
// LOOP
//======================================================

void loop()
{
    // Read all sensors
    readSensors();

    //----------------------------------------------
    // Send to server every 5 seconds
    //----------------------------------------------
    if (millis() - lastSend >= sendInterval)
    {
        lastSend = millis();
        sendToServer();
    }

    //----------------------------------------------
    // Serial Monitor
    //----------------------------------------------
    Serial.println("================================================");

    Serial.print("Temperature : ");
    Serial.print(temperature,1);
    Serial.println(" C");

    Serial.print("Humidity    : ");
    Serial.print(humidity,1);
    Serial.println(" %");

    Serial.print("MQ2         : ");
    Serial.println(mq2Value);

    Serial.print("MQ5         : ");
    Serial.println(mq5Value);

    Serial.print("Dust(IR)    : ");
    Serial.println(dustIR);

    Serial.print("AQI         : ");
    Serial.println(estimatedAQI,1);

    Serial.print("Air Status  : ");
    Serial.println(airStatus);

    Serial.print("Gas Status  : ");
    Serial.println(gasStatus);

    Serial.println("================================================");

    //----------------------------------------------
    // LCD PAGE 1
    //----------------------------------------------
    lcd.clear();
    lcd.setCursor(0,0);
    lcd.print("T:");
    lcd.print(temperature,1);
    lcd.print("C");
    lcd.setCursor(9,0);
    lcd.print("H:");
    lcd.print((int)humidity);
    lcd.print("%");
    lcd.setCursor(0,1);
    lcd.print("AQI:");
    lcd.print((int)estimatedAQI);
    delay(3000);

    //----------------------------------------------
    // LCD PAGE 2
    //----------------------------------------------
    lcd.clear();
    lcd.setCursor(0,0);
    lcd.print("MQ2:");
    lcd.print(mq2Value);
    lcd.setCursor(0,1);
    lcd.print("MQ5:");
    lcd.print(mq5Value);
    delay(3000);

    //----------------------------------------------
    // LCD PAGE 3
    //----------------------------------------------
    lcd.clear();
    lcd.setCursor(0,0);
    lcd.print("Gas:");
    lcd.print(gasStatus);
    lcd.setCursor(0,1);
    lcd.print(airStatus);
    delay(3000);

    //----------------------------------------------
    // LCD PAGE 4
    //----------------------------------------------
    lcd.clear();
    lcd.setCursor(0,0);
    lcd.print("Dust:");
    lcd.print(dustIR);
    lcd.setCursor(0,1);

    if(dustIR < 100)
        lcd.print("Very Clean");
    else if(dustIR < 300)
        lcd.print("Clean");
    else if(dustIR < 600)
        lcd.print("Moderate");
    else if(dustIR < 1000)
        lcd.print("Dusty");
    else
        lcd.print("Heavy Dust");
    delay(3000);

    //----------------------------------------------
    // LCD PAGE 5
    //----------------------------------------------
    lcd.clear();
    if(gasStatus=="SAFE")
    {
        lcd.setCursor(0,0);
        lcd.print("AIR IS SAFE");
        lcd.setCursor(0,1);
        lcd.print("Enjoy :)");
    }
    else if(gasStatus=="LOW")
    {
        lcd.setCursor(0,0);
        lcd.print("LOW GAS");
        lcd.setCursor(0,1);
        lcd.print("Be Careful");
    }
    else if(gasStatus=="MEDIUM")
    {
        lcd.setCursor(0,0);
        lcd.print("WARNING!");
        lcd.setCursor(0,1);
        lcd.print("Open Window");
    }
    else
    {
        lcd.setCursor(0,0);
        lcd.print("DANGER GAS!");
        lcd.setCursor(0,1);
        lcd.print("Evacuate!");
    }
    delay(3000);
}
