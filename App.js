import React, { useState, useEffect } from "react";
import { StatusBar } from "expo-status-bar";
import { StyleSheet, Text, View } from "react-native";
import SplashScreen from "./components/SplashScreen";
import Index from "./components/Index";
import Login from "./components/Login";
import Invoice from "./components/Invoice";
import Profile from "./components/Profile";
import Registration from "./components/Registration";
import AsyncStorage from '@react-native-async-storage/async-storage';
import { NavigationContainer } from "@react-navigation/native";
import { createNativeStackNavigator } from "@react-navigation/native-stack";

const Stack = createNativeStackNavigator();

export default function App() {

  const [userName, setUserName] = useState("");
  const [isLogged, setLogged] = useState('Login');

  const getUser = async () => {
    // AsyncStorage.clear();
    try {
      const value = await AsyncStorage.getItem('user_name');
      if (value !== null) {
        setUserName(value);
      }
    } catch (e) {
      // error reading value
    }

    // if(userName != ''){
    //   navigation.navigate("Index");
    // }
    // else{
    //   navigation.navigate("Login");
    // }
    
  };

  useEffect( () => {
    getUser();
  });

  return (
    <NavigationContainer>

          {/* <Stack.Navigator {...{initialRouteName: isLogged ? 'Index' : 'Login'}}> */}
          <Stack.Navigator initialroutename="SplashScreen">
            <Stack.Screen name="SplashScreen" component={SplashScreen} options={{headerShown: false}} />
            <Stack.Screen name="Index" component={Index} options={{headerShown: false}} />
            <Stack.Screen name="Login" component={Login} options={{headerShown: false}} />
            <Stack.Screen name="Invoice" component={Invoice} options={{headerShown: false}} />
            <Stack.Screen name="Profile" component={Profile} options={{headerShown: false}} />
            <Stack.Screen name="Registration" component={Registration} options={{headerShown: false}} />
          </Stack.Navigator>

    </NavigationContainer>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    alignItems: "center",
    justifyContent: "center",
  },
});
