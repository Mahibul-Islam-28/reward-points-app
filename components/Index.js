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
  const [user_id, setUserID] = useState("");

  const getData = async () => {
    try {
      const value = await AsyncStorage.getItem('user_id');
      if (value !== null) {
        setUserID(value);
      }
    } catch (e) {
      // error reading value
    }

    const url = "http://192.168.0.114:8000/api/get_points";

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
        const result = await response.json()
        const results = []
        
        result.forEach((invoice, index) => {
          results.push(
              <View style={styles.tableRow} key={index}>
                <Text style={styles.tableRowText}>{invoice.id}</Text>
                <Text style={styles.tableRowText}>{invoice.shop_name}</Text>
                <Text style={styles.tableRowText}>{invoice.invoice_id}</Text>
                <Text style={styles.tableRowText}>{invoice.category}</Text>
                <Text style={styles.tableRowText}>{invoice.amount}</Text>
                <Text style={styles.tableRowText}>{invoice.date}</Text>
                <Text style={styles.tableRowText}>{invoice.points}</Text>
              </View>
            );
          });

          */

          setInvoice(results)
  
      }
      else throw new Error('HTTP response status not code 200 as expected.');
    })
    .catch(function(error) {
        console.warn(error);
    });

  };

  useEffect( () => {
    getData()
    if(user_id)
    {
      getInvoice();
    }
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

          <View style={styles.table}>
            <View style={styles.tableHead}>
                <Text style={styles.tableHeadText}>ID</Text>
                <Text style={styles.tableHeadText}>Shop Name</Text>
                <Text style={styles.tableHeadText}>Invoice ID</Text>
                <Text style={styles.tableHeadText}>Category</Text>
                <Text style={styles.tableHeadText}>Amount</Text>
                <Text style={styles.tableHeadText}>Date</Text>
                <Text style={styles.tableHeadText}>Points</Text>
            </View>
            <View>{invoice}</View>
          </View>

      </View>    
    </View>
  );
};

export default Index;
