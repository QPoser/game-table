import { GET_PHASES } from "../actions/types";

const initialState = {
  phases: [],
  phase: {}
};

export default function(state = initialState, action) {
  switch (action.type) {
    case GET_PHASES:
      return {
        ...state,
        phases: action.payload
      };
    default:
      return state;
  }
}