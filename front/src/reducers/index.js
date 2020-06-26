import { combineReducers } from "redux";
import errorReducer from "./errorReducer";
import securityReducer from "./securityReducer";
import gameReducer from "./gameReducer";

export default combineReducers({
  errors: errorReducer,
  security: securityReducer,
  games: gameReducer
});
