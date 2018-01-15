 /*
  Created by Patrice Becquet
   Ref :  http://iot-playground.com for details
   Please use community fourum on website do not contact author directly
   
   Code based on https://github.com/DennisSc/easyIoT-ESPduino/blob/master/sketches/ds18b20.ino
   
   External libraries:
   - https://github.com/adamvr/arduino-base64
   - https://github.com/milesburton/Arduino-Temperature-Control-Library
   
   This program is free software; you can redistribute it and/or
   modify it under the terms of the GNU General Public License
   version 2 as published by the Free Software Foundation.

   You can adjust the number of probes, just don't forget the bus limit. 
   A good enhancement would be to POST all in one. 

 */
#include <ESP8266WiFi.h>
#include <OneWire.h>
#include <DallasTemperature.h>

//AP definitions
#define AP_SSID "YourAPSSID"
#define AP_PASSWORD "YourAPPwd"
#define AP_NAME "Dummy"     // Put a funny name here

// EasyIoT server definitions
#define EIOT_USERNAME    "user"
#define EIOT_PASSWORD    "pwd"
#define EIOT_IP_ADDRESS  "192.168.0.xx"         // Obviously an internet IP
#define EIOT_PORT        80
#define EIOT_APIKEY      "YourAPI Key"          // Same secret number in the API.
#define MAX_SONDES         2      // To be reUsed in loops.. Must no exceed number of values in sondesArray
#define REPORT_INTERVAL 300       // in sec
#define ONE_WIRE_BUS 2            // DS18B20 pin - D4 du Wemos qui correspond au GPIO2
OneWire oneWire(ONE_WIRE_BUS);
DallasTemperature DS18B20(&oneWire);

// arrays to hold device addresses
DeviceAddress devices[MAX_SONDES];
String sondesStrID[MAX_SONDES];
int devicesFound = 0;
float tempVals[MAX_SONDES];


#define USER_PWD_LEN 40
char unameenc[USER_PWD_LEN];
// float oldTemp[MAX_SONDES];

void wifiConnect()
{
    Serial.print("Connecting to AP");
    WiFi.begin(AP_SSID, AP_PASSWORD);
    WiFi.hostname(AP_NAME);
    while (WiFi.status() != WL_CONNECTED) {
    delay(1000);
    Serial.print(".");
  }
  
  Serial.println("");
  Serial.println("WiFi connected");  
}

// 
// function to print a device address : Example : 28FF0447A415042B
void printAddress(DeviceAddress deviceAddress)
{
  for (uint8_t i = 0; i < 8; i++)
  {
    // zero pad the address if necessary
    if (deviceAddress[i] < 16) Serial.print("0");
    Serial.print(deviceAddress[i], HEX);
  }
}

/*
 * Convert Probe Adress from 16 Hex to String
 */
char *addr2str(DeviceAddress deviceAddress)
{
    static char return_me[18];
    static char *hex = "0123456789ABCDEF";
    uint8_t i, j;

    for (i=0, j=0; i<8; i++) 
    {
         return_me[j++] = hex[deviceAddress[i] / 16];
         return_me[j++] = hex[deviceAddress[i] & 15];
    }
    return_me[j] = '\0';

    return (return_me);
}

void sendTemperature(float temp, int NumSonde, String sondeID)
{  
   WiFiClient client;
   
   while(!client.connect(EIOT_IP_ADDRESS, EIOT_PORT)) {
    Serial.println("connection failed");
    wifiConnect(); 
  }

  // Internal Syno url : http://192.168.0.10/
  String url = "";
  url += "/home_temp/writeTempAPI.php?apikey="+String(EIOT_APIKEY)+"&captID="+NumSonde+"&temp="+String(temp)+"&sondeID="+sondeID; // generate server node URL

  Serial.print("POST data to URL: ");
  Serial.println(url);
  
  client.print(String("POST ") + url + " HTTP/1.1\r\n" +
               "Host: " + String(EIOT_IP_ADDRESS) + "\r\n" + 
               "Connection: close\r\n" + 
      //         "Authorization: Basic " + unameenc + " \r\n" + 
               "Content-Length: 0\r\n" + 
               "\r\n");

  delay(100);
    while(client.available()){
    String line = client.readStringUntil('\r');
    Serial.print(line);
  }
  Serial.println();
  // print your WiFi shield's IP address:
  Serial.print("IP Address: ");
  Serial.println(WiFi.localIP());
  Serial.println();
  Serial.println("Connection closed");
}

void setup() {
  Serial.begin(115200);
  DS18B20.begin();
  devicesFound = DS18B20.getDeviceCount(); 
  Serial.print("# Devices found : ");
  Serial.println(devicesFound);

  for (int i=0;i<devicesFound;i++) {
    if (!DS18B20.getAddress(devices[i], i)) {
      Serial.print("Unable to find address for Device ");
      Serial.println(i);
    } else {
      Serial.print("Device Address  ");
      Serial.print(i);
      Serial.print(" : ");
      // printAddress(devices[i]);
      sondesStrID[i] = addr2str(devices[i]);
      Serial.print(sondesStrID[i]);
      Serial.println();
    }
  }

  wifiConnect();
  /* 
  char uname[USER_PWD_LEN];
  String str = String(EIOT_USERNAME)+":"+String(EIOT_PASSWORD);  
  str.toCharArray(uname, USER_PWD_LEN); 
  memset(unameenc,0,sizeof(unameenc));
  //base64_encode(unameenc, uname, strlen(uname)); // Bad lib called, or find this one..
  //encode_base64(unameenc, strlen(uname), uname);
  */
  /*
  for (int i=0;i<devicesFound;i++) {
    oldTemp[i] = -1;
  } 
  */
}

void loop() {
  float temp;
  int cnt = REPORT_INTERVAL;
  for (int i=0;i<devicesFound;i++) {
    
    DS18B20.requestTemperatures(); 
    temp = DS18B20.getTempCByIndex(i);
    Serial.print("Temperature: ");
    Serial.println(temp);

    if (temp != 85.0 && temp != (-127.0)) 
    {
      // if (temp != oldTemp[i])
      // {
        sendTemperature(temp, i, sondesStrID[i]);
      //  oldTemp[i] = temp;
      // }
    }
  }
  while(cnt--) delay(1000);
}

