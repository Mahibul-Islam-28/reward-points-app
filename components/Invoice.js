import React, { useState, useEffect } from "react";
import { Text,  View, TextInput, Button, Platform } from "react-native";
import DatePicker from 'react-native-datepicker';
import DateTimePicker from '@react-native-community/datetimepicker';
import Menu from "./Menu";
import SelectDropdown from 'react-native-select-dropdown';
import { styles } from "./Style";
import AsyncStorage from '@react-native-async-storage/async-storage';
// import {
//   ANDROID_MODE,
//   IOS_MODE,
// } from './css/constants';


const Invoice = () => {

  // const MODE_VALUES = Platform.select({
  //   ios: Object.values(IOS_MODE),
  //   android: Object.values(ANDROID_MODE),
  //   windows: [],
  // });

  // console.log("MODE_VALUES: ",MODE_VALUES);
  

  
  const [shop_name, setShopName] = useState("");
  const [invoice_id, setInvoiceID] = useState("");
  const [amount, setAmount] = useState("");
  const [category, setCategory] = useState("");
  const [payment, setPayment] = useState("");
  const [user_id, setUserID] = useState("");
  const categories = ["Food", "Dress", "Tech"];
  const [date, setDate] = useState("");
  // const sourceDate = sourceMoment.local().toDate();
  // const sourceMoment = moment.unix(1636765200);
  // const [date, setDate] = useState(sourceDate);
  // const [mode, setMode] = useState(MODE_VALUES[0]);

  // const onChange = (e, selectedDate) => {
  //   setDate(selectedDate)
  // }
  // const showMode = (modeToShow) => {
  //   setShow(true);
  //   setMode(modeToShow);
  // }

  const getUser = async () => {
    try {
      const value = await AsyncStorage.getItem('user_id');
      if (value !== null) {
        setUserID(value);
      }
    } catch (e) {
      // error reading value
    }
  };


  useEffect( () => {
    getUser();
  });

    const saveData = async () => {
        const url = "http://192.168.0.114:8000/api/create_invoice";
        let result = await fetch(url, {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify({ user_id, shop_name, invoice_id, date, amount, category, payment }),
        });
        if (result) {
          navigation.navigate("Index");
        } else {
          console.log("Failed");
        }
      };

  return (
    <View>
      <View>
        <Menu />
      </View>
      <View style={styles.container}>
        <View style={styles.invoiceForm}>
            <Text style={styles.formTitle}> Invoice </Text>
            <View>
              <TextInput
                style={styles.input}
                onChangeText={(text) => setShopName(text)}
                value={shop_name}
                placeholder="Shop Name.."
              />
              <TextInput
                style={styles.input}
                onChangeText={(text) => setInvoiceID(text)}
                value={invoice_id}
                placeholder="Invoice ID.."
              />
              <TextInput
                style={styles.input}
                onChangeText={(text) => setDate(text)}
                value={date}
                placeholder="Date.."
              />
              <SelectDropdown
                style={styles.dropDown}
                buttonStyle={{
                  backgroundColor: "transparent",
                  borderColor: '#333',
                  borderStyle: 'solid',
                  borderWidth: 1,
                  width: "100%",
                }}
                dropdownStyle={{
                  backgroundColor: "#fff",
                  marginBottom: 0,
                  paddingBottom: 0,
                  borderBottomWidth: 0
                }}
                rowStyle={{
                  paddingVertical: 10,
                }}
                data={categories}
                onSelect={(selectedItem, index) => {
                  setCategory(selectedItem);
                }}
                buttonTextAfterSelection={(selectedItem, index) => {
                  return selectedItem
                }}
                rowTextForSelection={(item, index) => {
                  return item
                }}
              />


                {/* <DateTimePicker
                  style={styles.datePickerStyle}
                  value={date}
                  mode='date'
                  onChange={onChange}  
                /> */}
            {/* <DatePicker
              style={styles.datePickerStyle}
              date={date} // Initial date from state
              mode="date" // The enum of date, datetime and time
              placeholder="select date"
              format="DD-MM-YYYY"
              minDate="01-01-2016"
              maxDate="01-01-2019"
              confirmBtnText="Confirm"
              cancelBtnText="Cancel"
              customStyles={{
                dateIcon: {
                  //display: 'none',
                  position: 'absolute',
                  left: 0,
                  top: 4,
                  marginLeft: 0,
                },
                dateInput: {
                  marginLeft: 36,
                  width: 100
                },
              }}
              onDateChange={(date) => {
                setDate(date);
              }}
            /> */}

              {/* <DateTimePicker
                testID="dateTimePicker"
                timeZoneOffsetInMinutes={tzOffsetInMinutes}
                timeZoneName={tzName}
                minuteInterval={interval}
                maximumDate={maximumDate}
                minimumDate={minimumDate}
                value={date}
                mode={mode}
                is24Hour
                display={display}
                onChange={onChange}
                textColor={textColor || undefined}
                accentColor={accentColor || undefined}
                neutralButton={{label: neutralButtonLabel}}
                negativeButton={{label: 'Cancel', textColor: 'red'}}
                disabled={disabled}
              /> */}

              <TextInput
                style={styles.input}
                onChangeText={(text) => setAmount(text)}
                value={amount}
                placeholder="Amount.."
              />
              <TextInput
                style={styles.input}
                onChangeText={(text) => setPayment(text)}
                value={payment}
                placeholder="Payment Method"
              />
              <Button
                style={styles.submitBtn}
                title="Submit"
                onPress={saveData}
              />
            </View>
          </View>
      
      </View>
    </View>
  );
};

export default Invoice;
