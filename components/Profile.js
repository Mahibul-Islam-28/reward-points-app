import React, { useState, useEffect } from "react";
import { Text,  View, Image} from "react-native";
import Menu from "./Menu";
import { styles } from "./Style";
import AsyncStorage from '@react-native-async-storage/async-storage';

const Profile = () => {
  const userImage = require("../assets/profile-user.png");
    return (
    <View>
      <View>
        <Menu />
      </View>
      <View style={styles.container}>
        <Image 
          source={userImage}
          style={styles.logo}
        />
        <Text><b>Name:</b> Mr. Jon Snow</Text>
      </View>
    </View>
)};

export default Profile;