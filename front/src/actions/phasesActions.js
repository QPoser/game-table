import axios from "axios";
import { GET_PHASES, 
  SET_SELECTED_PHASES,SET_PHASE_IN_PROGRESS, 
  SET_STATE_OF_CURRENT_PHASE, SET_ANSWER_SELECTED_BY_USER_FROM_YOUR_TEAM } from "./types";
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
  
  };

  export const setCurrentPhase = (gameId, phaseName) => async dispatch => {
    const res = await axios.post("/api/game/quiz/" + gameId + "/phase", {
      "phase_type": phaseName
    });
  
    debugger

    dispatch(getCurrentGame());
  
  };


  export const setSelectedPhases = (selectedPhases) => async dispatch => {
    dispatch({
        type: SET_SELECTED_PHASES,
        payload: selectedPhases
      });
  
    dispatch(setPhaseInProgress(selectedPhases));
  };

  export const setPhaseInProgress = (selectedPhases) => async dispatch => {

    let phaseInProgress = selectedPhases.filter(phase => phase.status == "in_progress")[0];


    dispatch({
      type: SET_PHASE_IN_PROGRESS,
      payload: phaseInProgress
    });
  }


  export const setStateOfCurrentPhase = (stateOfCurrentPhase) => async dispatch => {
    dispatch({
      type: SET_STATE_OF_CURRENT_PHASE,
      payload: stateOfCurrentPhase
    });
  }

  export const setAnswerSelectedByUserFromYourTeam = (selectedAnswer) => async dispatch => {
    dispatch({
      type: SET_ANSWER_SELECTED_BY_USER_FROM_YOUR_TEAM,
      payload: selectedAnswer 
    });
  }