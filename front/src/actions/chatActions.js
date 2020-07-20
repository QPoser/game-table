import axios from 'axios';
import {
    GET_CHATS,
    AFTER_POST_MESSAGE,
    GET_MESSAGES
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

export const getMessages = (gameId) => async dispatch => {
    const res = await axios.get("/api/chat/" + gameId + "/messages");
    
    
    dispatch({
      type: GET_MESSAGES,
      payload: res.data
    });
  };

  export const postMessage = (message) => async dispatch => {
      const res = await axios.post("/api/chat/" + message.gameId + "/message", {
          content: message.content,
          type: message.recipient
      });

      dispatch(getMessages(message.gameId));

  }

