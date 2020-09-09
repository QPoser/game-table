import { combineReducers } from "redux";
import errorReducer from "./errorReducer";
import securityReducer from "./securityReducer";
import gameReducer from "./gameReducer";
import chatReducer from "./chatReducer";
import phaseReducer from "./phaseReducer";

export default combineReducers({
  errors: errorReducer,
  security: securityReducer,
  games: gameReducer,
  messages: chatReducer,
  phases: phaseReducer
});
