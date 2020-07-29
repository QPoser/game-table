import { GET_PHASES, SET_SELECTED_PHASES } from "../actions/types";

const initialState = {
  phases: [],
  selectedPhases: [],
  phase: {}
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
    default:
      return state;
  }
}