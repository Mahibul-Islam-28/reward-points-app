import React, { useEffect, useState } from "react";
import { Text, StyleSheet, View, TextInput, Button, Linking } from "react-native";
import AsyncStorage from '@react-native-async-storage/async-storage';
import { styles } from "./Style";


// const Stack = createStackNavigator();

const Login = ({navigation}) => {
  const [user_name, setUserName] = useState("");
  const [password, setPassword] = useState("");

  const storeUserName = async (value) => {
    try {
      // const jsonValue = JSON.stringify(value);
      await AsyncStorage.setItem('user_name', value);
    } catch (e) {
      // saving error
    }
  };
  const storeUserID = async (value) => {
    try {
      await AsyncStorage.setItem('user_id', value);
    } catch (e) {
      // saving error
    }
  };


  const loginData = async () => {
    setUserName("");
    setPassword("");
    const url = "http://192.168.0.114:8000/api/login";
    await fetch(url, {
      method: "POST",
      body: JSON.stringify({ user_name, password }),
      headers: {
        "Content-Type": "application/json",
      }
    }).then(async function(response) {
      if (response.status == 200) {
        const result = await response.json();
          // console.log(result);
          if(result > 0)
          {
            // console.log("Success");
            // const userInfo = {'user_name': user_name, 'password': password};
            storeUserName(user_name);
            storeUserID(result);
            navigation.navigate("Index");
          }
          else{
            console.log("User name and password not matched.")
          }
          
      }
      else throw new Error('HTTP response status not code 200 as expected.');
    })
    .catch(function(error) {
        console.warn(error);
    });
    // const result = await response.json();
    // console.log(result);
    
    // if(result == 1)
    // {
    //   console.log("Success");
    //   // navigation.navigate("Index");
    // }
    // else{
    //   console.log("User name and password not matched.")
    // }
  };

  // const getData = async () => {
  //   const myURL = "http://localhost:8000/api/get_points";
  //   const user_id = 1;
  //   let respo  = await fetch(myURL, {
  //     method: "POST",
  //     body: JSON.stringify({ user_id }),
  //     headers: {
  //       "Content-Type": "application/json",
  //     }
  //   });
  //   console.warn(respo);
  //   const res = await respo.json;
  //   console.log(res);
    
  //   // setPoints(result);
  // }

  // const getUser = async () => {
  //   try {
  //     const value = await AsyncStorage.getItem('user-info');
  //     if (value !== null) {
  //       console.log(Object.values(value));
  //     }
  //     else{
  //       console.log("noooo");
  //     }
  //   } catch (e) {
  //     // error reading value
  //   }
  // };

  // useEffect( () => {
  //   getUser();
  // })

  return (
    <View style={styles.container}>
      <View style={styles.loginForm}>
        <Text style={styles.formTitle}> Login </Text>
        <View>
          <TextInput
            style={styles.input}
            onChangeText={(text) => setUserName(text)}
            placeholder="Username"
            value={user_name}
          />
          <TextInput
            style={styles.input}
            onChangeText={(text) => setPassword(text)}
            placeholder="Password"
            value={password}
          />
          <Button style={styles.submitBtn} title="Login" onPress={loginData} />

          {/* <Text title="Login" onPress={() => Linking.openURL('/home')} >Hello</Text> */}
        </View>
      </View>

      <View style={{flexDirection: 'row', marginTop: 20}}>
        {/* <Text>{points}</Text> */}
        <Text>Don't have an account? </Text>
        <Text onPress={() => navigation.navigate('Registration')}>Register? </Text>
      </View>

    </View>
  );
};

export default Login;
