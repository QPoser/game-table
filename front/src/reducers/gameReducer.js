import { GET_GAMES } from "../actions/types";

const initialState = {
  games: [],
  game: {}
};

export default function(state = initialState, action) {
  switch (action.type) {
    case GET_GAMES:
      return {
        ...state,
        games: action.payload
      };
    default:
      return state;
  }
}