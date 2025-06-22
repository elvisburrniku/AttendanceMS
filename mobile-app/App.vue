<template>
  <view class="container">
    <stack-navigator>
      <screen name="Login" component="LoginScreen" />
      <screen name="Dashboard" component="DashboardScreen" />
      <screen name="CheckIn" component="CheckInScreen" />
      <screen name="History" component="HistoryScreen" />
    </stack-navigator>
  </view>
</template>

<script>
import { StackNavigator, Screen } from 'vue-native-router'
import LoginScreen from './src/screens/LoginScreen.vue'
import DashboardScreen from './src/screens/DashboardScreen.vue'
import CheckInScreen from './src/screens/CheckInScreen.vue'
import HistoryScreen from './src/screens/HistoryScreen.vue'

export default {
  components: {
    StackNavigator,
    Screen,
    LoginScreen,
    DashboardScreen,
    CheckInScreen,
    HistoryScreen
  }
}
</script>

<style>
.container {
  flex: 1;
  background-color: #f5f5f5;
}
</style>