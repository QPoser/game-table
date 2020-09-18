import { GET_PHASES, SET_SELECTED_PHASES, 
  SET_PHASE_IN_PROGRESS, 
  SET_STATE_OF_CURRENT_PHASE, SET_ANSWER_SELECTED_BY_USER_FROM_YOUR_TEAM } from "../actions/types";

const initialState = {
  phases: [],
  selectedPhases: [],
  phase: {},
  phaseInProgress: {},
  stateOfCurrentPhase: "",
  answerSelectedByUserFromYourTeam: ""
};

export default function(state = initialState, action) {
  switch (action.type) {
    case GET_PHASES:
      return {
        ...state,
        phases: action.payload
      };
    case SET_SELECTED_PHASES:
      return {
        ...state,
        selectedPhases: action.payload
      }  
    case SET_PHASE_IN_PROGRESS:
      return {
        ...state,
        phaseInProgress: action.payload
      } 
    case SET_STATE_OF_CURRENT_PHASE:
      return {
        ...state,
        stateOfCurrentPhase: action.payload
      }  
      case SET_ANSWER_SELECTED_BY_USER_FROM_YOUR_TEAM:
        return {
          ...state,
          answerSelectedByUserFromYourTeam: action.payload
        }   
    default:
      return state;
  }
}