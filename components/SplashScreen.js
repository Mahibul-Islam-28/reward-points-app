// Import React and Component
import React, {useState, useEffect} from 'react';
import {
  ActivityIndicator,
  View,
  StyleSheet,
  Image
} from 'react-native';

import AsyncStorage from '@react-native-async-storage/async-storage';

const SplashScreen = ( {navigation} ) => {
  //State for ActivityIndicator animation
  const [animating, setAnimating] = useState(true);

  const staticImage = require("../assets/logo.png");

  const getUser = async () => {
    // AsyncStorage.clear();
    try {
      const value = await AsyncStorage.getItem('user_name');
      console.log(value);
      if(value == null){
        navigation.navigate("Login");
      }
      else{
        navigation.navigate("Index");
      }
    } catch (e) {
      console.log('Not Found');
    }
  };

  useEffect( () => {

    setTimeout(() => {
      getUser();
    }, 2000);
    


    // if(userName != ''){
    //   navigation.navigate("Index");
    // }
    // else{
    //   navigation.navigate("Login");
    // }

    // setTimeout(() => {
    //   setAnimating(false);
    //   // AsyncStorage.getItem('user_id').then((value) =>
    //   //   navigation.replace(
    //   //     value === null ? 'Auth' : 'DrawerNavigationRoutes'
    //   //   ),
    //   // );

    // }, 5000);
  }, []);

  return (
    <View style={styles.container}>
       <Image
        source={staticImage}
        style={styles.logo}
      />
      <ActivityIndicator
        animating={animating}
        color="#FFFFFF"
        size="large"
        style={styles.activityIndicator}
      />
    </View>
  );
};

export default SplashScreen;

const styles = StyleSheet.create({
  container: {
    flex: 1,
    alignItems: 'center',
    justifyContent: 'center',
    backgroundColor: '#45B3E0',
  },
  activityIndicator: {
    alignItems: 'bottom',
    height: 200,
  },
  logo: {
    resizeMode: "contain",
    width: 100,
    height: 100,
    alignItems: "center",
  }
});