import axios from 'axios';
import {
    GET_CHATS,
    AFTER_POST_MESSAGE
} from './types';


export function getChats(){
    const request = axios.get("fff/getChats")
        .then(response => response.data);
    
    return {
        type: GET_CHATS,
        payload: request
    }
}

export function afterPostMessage(data){

    return {
        type: AFTER_POST_MESSAGE,
        payload: data
    }
}

export const getMessage = () => async dispatch => {
    const res = await axios.get("/api/chat/" + "1" + "/messages");
    debugger
    /*
    dispatch({
      type: GET_ROOMS,
      payload: res.data
    });
    */
  };

