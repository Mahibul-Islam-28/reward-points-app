import React, { useState, useEffect } from "react";
import { Text,  View, Image} from "react-native";
import Menu from "./Menu";
import { styles } from "./Style";
import AsyncStorage from '@react-native-async-storage/async-storage';

const Profile = () => {

  const [userData, setUserData] = useState([]);
  const [user_id, setUserID] = useState("");

  const getUser = async () => {
    const url = "http://192.168.0.114:8000/api/get_user";
    try {
      const value = await AsyncStorage.getItem('user_id');
      if (value !== null) {
        setUserID(value);
      }
    } catch (e) {
      // error reading value
    }
    console.log(user_id);
    if(user_id){
      let response  = await fetch(url, {
        method: "POST",
        body: JSON.stringify({ user_id }),
        headers: {
          "Content-Type": "application/json",
        }
      }).then(async function(response) {
        if (response.status == 200) {
          const result = await response.json();
          setUserData(result);
          console.log(userData);
            
        }
        else throw new Error('HTTP response status not code 200 as expected.');
      })
      .catch(function(error) {
          console.warn(error);
      });
    };
    console.log(userData);

    }



  useEffect( () => {
    getUser();
  });


    return (
    <View>
      <View>
        <Menu />
      </View>
      <View style={styles.container}>
        <Image 
          source={require("../assets/profile-user.png")}
          style={{width: 50, height: 50}}
        />
        <Text><b>Name:</b> {userData.name}</Text>
        <Text><b>Email:</b> {userData.email}</Text>
        <Text><b>Phone:</b> {userData.phone}</Text>
      </View>
    </View>
)};

export default Profile;