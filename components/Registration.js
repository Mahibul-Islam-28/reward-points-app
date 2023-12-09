import React, { useState } from "react";
import { Text, StyleSheet, View, TextInput, Button } from "react-native";
import { styles } from "./Style";

const Registration = ({ navigation }) => {
  const [name, setName] = useState("");
  const [phone, setPhone] = useState("");
  const [email, setEmail] = useState("");
  const [user_name, setUserName] = useState("");
  const [password, setPassword] = useState("");

  const saveData = async () => {
    const url = "http://192.168.0.114:8000/api/registration";
    let result = await fetch(url, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({ name, phone, email, user_name, password }),
    });
    if (result) {
      navigation.navigate("Login");
    } else {
      console.log("Failed");
    }
  };

  return (
    <View style={styles.container}>
      <View style={styles.registerForm}>
        <Text style={styles.formTitle}> Registration </Text>
        <View>
          <TextInput
            style={styles.input}
            onChangeText={(text) => setName(text)}
            value={name}
            placeholder="Name.."
          />
          <TextInput
            style={styles.input}
            onChangeText={(text) => setPhone(text)}
            value={phone}
            placeholder="Phone.."
          />
          <TextInput
            style={styles.input}
            onChangeText={(text) => setEmail(text)}
            value={email}
            placeholder="Email.."
          />
          <TextInput
            style={styles.input}
            onChangeText={(text) => setUserName(text)}
            value={user_name}
            placeholder="User Name.."
          />
          <TextInput
            style={styles.input}
            onChangeText={(text) => setPassword(text)}
            value={password}
            placeholder="Password"
          />
          <Button
            style={styles.submitBtn}
            title="Register"
            onPress={saveData}
          />
        </View>
      </View>
      <View style={{flexDirection: 'row', marginTop: 20}}>
        <Text>Already have an account? </Text>
        <Text onPress={() => navigation.navigate('Login')}>Login? </Text>
      </View>
    </View>
  );
};

export default Registration;
