import { GET_PHASES, SET_SELECTED_PHASES, SET_PHASE_IN_PROGRESS } from "../actions/types";

const initialState = {
  phases: [],
  selectedPhases: [],
  phase: {},
  phaseInProgress: {}
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
    default:
      return state;
  }
}