import axios from "axios";
import { GET_PHASES, SET_SELECTED_PHASES } from "./types";
import { getCurrentGame } from "./gamesActions";

export const getPhases = () => async dispatch => {
    const res = await axios.get("/api/game/quiz/phases");
  
  
    
  
  
    var keys = Object.keys(res.data.data);
  
  
    var phases = [];
    keys.forEach(function (key) {
    for(var phas in res.data.data[key]) {
      if (res.data.data[key].hasOwnProperty(phas)) {
        phases.push({ "type":key, "name": res.data.data[key][phas]});
      }
    }
    });
  
    

    dispatch({
        type: GET_PHASES,
        payload: phases
      });


    /*
    dispatch({
      type: GET_GAMES,
      payload: res.data
    });
    */
  
  };

  export const setCurrentPhase = (gameId, phaseName) => async dispatch => {
    const res = await axios.post("/api/game/quiz/" + gameId + "/phase", {
      "phase_type": phaseName
    });
  
  
    debugger

    dispatch(getCurrentGame());

    /*
    dispatch({
      type: GET_GAMES,
      payload: res.data
    });
    */
  
  };


  export const setSelectedPhases = (selectedPhases) => async dispatch => {

    dispatch({
        type: SET_SELECTED_PHASES,
        payload: selectedPhases
      });
  
  };