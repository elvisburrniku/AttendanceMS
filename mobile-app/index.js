import { AppRegistry } from 'react-native';
import { VueNativeHelper } from 'vue-native-helper';

import App from './App.vue';
import { name as appName } from './app.json';

const vueApp = VueNativeHelper(App);

AppRegistry.registerComponent(appName, () => vueApp);