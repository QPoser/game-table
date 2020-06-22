import axios from "axios";
import { GET_ROOMS, JOIN_ROOM, GET_ERRORS } from "./types";

export const getRooms = () => async dispatch => {
    const res = await axios.get("/api/rooms");
    dispatch({
      type: GET_ROOMS,
      payload: res.data
    });
  };

  export const join = JoinRequest => async dispatch => {
    try {
   
      const res = await axios.post("/api/rooms/" + JoinRequest.id + "/join", { password: JoinRequest.password });
     
      debugger
 
 
    } catch (err) {

    }
  }; 
  
//addNewRoom 

export const createNewRoom = (newUser, history) => async dispatch => {
  try {
    await axios.post("/api/rooms/create", newUser);
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