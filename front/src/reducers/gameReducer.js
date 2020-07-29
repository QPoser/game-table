import { GET_GAMES, SET_CURRENT_GAME, SET_GAME_STATE } from "../actions/types";

const initialState = {
  games: [],
  gameState: "",
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
    case SET_GAME_STATE: 
        return {
          ...state,
          gameState: action.payload
        }  
    default:
      return state;
  }
}