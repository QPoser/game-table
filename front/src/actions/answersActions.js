import axios from "axios";
import { POST_ANSWER } from "./types";
import { getCurrentGame } from "./gamesActions";



  export const postAnswer = (gameId, answerName) => async dispatch => {
    const res = await axios.post("/api/game/quiz/" + gameId + "/answer", {
      //"answer": "answer 1"
      "answer": answerName
    });
  
    

    dispatch(getCurrentGame());
  
  };


