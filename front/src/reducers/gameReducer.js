import { GET_GAMES, SET_CURRENT_GAME } from "../actions/types";

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
    case SET_CURRENT_GAME:
      return {
        ...state,
        game: action.payload
      };
    default:
      return state;
  }
}