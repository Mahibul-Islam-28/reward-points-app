import React, { useState, useEffect } from "react";
import { Text, View } from "react-native";
import { styles } from "./Style";
import Menu from "./Menu";
import AsyncStorage from '@react-native-async-storage/async-storage';
import { Button } from "react-native-web";

const Index = ( {navigation} ) => {
  const [points, setPoints] = useState(0);
  const [invoice, setInvoice] = useState("");
  const [userName, setUserName] = useState("");
  const [results, setResults] = useState([]);

  const getData = async () => {
    const url = "http://192.168.0.114:8000/api/get_points";
    const user_id = 1;
    let response  = await fetch(url, {
      method: "POST",
      body: JSON.stringify({ user_id }),
      headers: {
        "Content-Type": "application/json",
      }
    }).then(async function(response) {
      if (response.status == 200) {
        const result = await response.json();
        setPoints(result)
          
      }
      else throw new Error('HTTP response status not code 200 as expected.');
    })
    .catch(function(error) {
        console.warn(error);
    });
  };

  const getInvoice = async () => {
    const url = "http://192.168.0.114:8000/api/get_invoice";
    const user_id = 1;
    let response  = await fetch(url, {
      method: "POST",
      body: JSON.stringify({ user_id }),
      headers: {
        "Content-Type": "application/json",
      }
    }).then(async function(response) {
      if (response.status == 200) {
        const result = await response.json();
        // const abc = Object.keys(result).map(key => ({[key]: result[key]}));

        // setInvoice(JSON.stringify(result));
        // setInvoice(JSON.parse(result));
        // setInvoice(result);

      const results = [];

      result.forEach((invoice, index) => {
        results.push(
            <tr style={styles.tableRow}>
              <td>{invoice.id}</td>
              <td>{invoice.shop_name}</td>
              <td>{invoice.invoice_id}</td>
              <td>{invoice.category}</td>
              <td>{invoice.amount}</td>
              <td>{invoice.date}</td>
              <td>{invoice.points}</td>
            </tr>
          );
        });

        setInvoice(results);


          
      }
      else throw new Error('HTTP response status not code 200 as expected.');
    })
    .catch(function(error) {
        console.warn(error);
    });

  };

  const getUser = async () => {
    try {
      const value = await AsyncStorage.getItem('user_name');
      if (value !== null) {
        setUserName(value);
      }
      else{
        console.log("noo");
      }
    } catch (e) {
      // error reading value
    }
  };

  const logout = () => {
    AsyncStorage.clear();
    navigation.navigate("Login");
  };

  useEffect( () => {
    getUser();
    getData();
    getInvoice();
  });

  return (
    <View>
        <View>
          <Menu />
        </View>
      <View style={styles.container}>

            <View style={styles.rewardCard}>
              <Text style={styles.number}>{points}</Text>
              <Text style={styles.cardText}>Your Points</Text>
            </View>


          <table style={styles.table}>
              <thead>
                  <tr style={styles.tableRow}>
                    <th>ID</th>
                    <th>Shop Name</th>
                    <th>Invoice ID</th>
                    <th>Category</th>
                    <th>Amount</th>
                    <th>Date</th>
                    <th>Points</th>
                  </tr>
              </thead>
              <tbody>
                {invoice}
              </tbody>
          </table>
      </View>    
    </View>
  );
};

export default Index;
