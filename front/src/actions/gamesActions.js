import axios from "axios";
import { getMessages } from "./chatActions";
import { getPhases } from "./phasesActions";
import { GET_GAMES, JOIN_GAME, SET_CURRENT_GAME, GET_ERRORS } from "./types";

export const getGames = () => async dispatch => {
    const res = await axios.get("/api/games");
    dispatch({
      type: GET_GAMES,
      payload: res.data
    });
  };

  export const getCurrentGame = () => async dispatch => {
    const res = await axios.get("/api/games/current");
    /*
    dispatch({
      type: GET_GAMES,
      payload: res.data
    });
    */
    debugger
    dispatch(setCurrentGame(res.data));

    dispatch(getMessages(res.data.data.id));

    dispatch(getPhases());

  };

  export const setCurrentGame = (game) => dispatch => {
  
    dispatch({
      type: SET_CURRENT_GAME,
      payload: game
    });
  };

  export const join = JoinRequest => async dispatch => {
    try {
   
      const res = await axios.post("/api/games/" + JoinRequest.id + "/join", 
      { 
        password: JoinRequest.password,
        team: JoinRequest.team  
      });
     
      debugger
 
 
    } catch (err) {

    }
  }; 

  export const leave = LeaveRequest => async dispatch => {
    try {
   
      const res = await axios.post("/api/games/" + LeaveRequest.id + "/leave");
     
      debugger
 
 
    } catch (err) {

    }
  }; 
  
//addNewRoom 

export const createNewGame = (newUser, history) => async dispatch => {
  try {
    await axios.post("/api/games/create", newUser);
    history.push("/dashboard");
    dispatch({
      type: GET_ERRORS,
      payload: {}
    });
  } catch (err) {
    dispatch({
      type: GET_ERRORS,
      payload: err.response.data
    });
  }
};






///


