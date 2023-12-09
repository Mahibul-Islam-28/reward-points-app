import React, { useState } from "react";
import { Text, StyleSheet, View, TextInput, Button } from "react-native";
import { styles } from "./Style";
import { TouchableOpacity } from "react-native-web";
import AsyncStorage from '@react-native-async-storage/async-storage';
import { useNavigation } from "@react-navigation/native";

const Menu = () => {

   const navigation = useNavigation(); 
  const logout = () => {
    AsyncStorage.clear();
    navigation.navigate("Login");
  };

  return (
    <View style={styles.menuContainer}>
      <Text onPress={() => navigation.navigate("Index")} style={styles.menuButton}>Home</Text>
      <Text onPress={() => navigation.navigate("Invoice")} style={styles.menuButton}>Invoice</Text>
      <Text onPress={() => navigation.navigate("Profile")} style={styles.menuButton}>Profile</Text>
      <Text onPress={() => logout() } style={styles.menuButton}>Logout</Text>
    </View>
  );
};

export default Menu;
