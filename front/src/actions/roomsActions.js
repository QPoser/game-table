import axios from "axios";
import { GET_ROOMS } from "./types";


export const getRooms = () => async dispatch => {
    const res = await axios.get("/api/rooms");
    dispatch({
      type: GET_ROOMS,
      payload: res.data
    });
  };