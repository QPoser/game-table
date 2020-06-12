import { combineReducers } from "redux";
import errorReducer from "./errorReducer";
import securityReducer from "./securityReducer";
import roomReducer from "./roomReducer";

export default combineReducers({
  errors: errorReducer,
  security: securityReducer,
  rooms: roomReducer
});
