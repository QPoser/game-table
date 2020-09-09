import React, { Component } from "react";
//import ProjectItem from "./Project/ProjectItem";
//import CreateProjectButton from "./Project/CreateProjectButton";
import { connect } from "react-redux";
import { getGames, setCurrentGame, getCurrentGame, setGameState } from "../actions/gamesActions";
import { getMessages, afterPostMessage } from "../actions/chatActions";
import PropTypes from "prop-types";
import Spinner from "./Spinner";
import Game from "./Game";
import { Link } from 'react-router-dom';
import io from "socket.io-client";
import { QUIZ_PLAYING_STARTED } from "../actions/types";

class Dashboard extends Component {
  
  constructor() {
    super();
  }

  componentDidMount() {
    this.props.getGames();
    this.socketInitialization();
  }

  socketInitialization() {
    
    const jwtToken = localStorage.jwtToken;
    
    let socket = io('http://127.0.0.1:8888', {
        transports: ['websocket']
    });

    socket.emit('private', { // subscribe to private channel
        'token': jwtToken
    });
    
    socket.on('notifications', function (data) {
       debugger
    });

    socket.on('game_action', function (data) {

       let msgBody = JSON.parse(data);
       if (msgBody.Template === 'your_game_started') {
        console.log('Game was started ' + msgBody.Game.Id);
        this.props.getMessages(msgBody.Game.Id);
        this.props.history.push("/gamechat");
       }
       this.props.getGames();
       debugger

       if (msgBody.Template === 'user_chose_phase_in_quiz'){
        this.props.getCurrentGame();
       }

       if (msgBody.Template === 'quiz_playing_started') {
         this.props.setGameState(QUIZ_PLAYING_STARTED)
       }

    }.bind(this));

    socket.on('chat', function (data) {
       debugger
       var msgBody = JSON.parse(data);
       this.props.getMessages(msgBody.Game.Id);
       debugger
    }.bind(this));

    socket.on('connect', (s) => {
      debugger
    });
  }


  render() {
    const { data = [] } = this.props.games.games;

    

    const content = (
      <div className="projects">
      <div className="container">
        <div className="row">
          <div className="col-md-12">
            <h1 className="display-4 text-center">Games</h1>
            <br />
              <Link to="/addgame">
                  <span className="btn btn-primary">Create game</span>
              </Link>
            <br />
            <hr />
            <div className="row lead mb-4">
                <div className="col-md-2">
                  #
                </div>
                <div className="col-md-6">
                  Game
                </div>
            </div>
            {data.map(game => (
              <Game key={game.id} game={game} />
            ))}
          </div>
        </div>
      </div>
    </div>
    ); 

    if (data.length === 0) {
      return <Spinner />
    } 

    return (
      <div>
        {content}
      </div>  
    );
  }
}

Dashboard.propTypes = {
  games: PropTypes.object.isRequired,
  getGames: PropTypes.func.isRequired
};

const mapStateToProps = state => ({
  games: state.games
});

export default connect(
  mapStateToProps,
  { getGames, setCurrentGame, getMessages, getCurrentGame, setGameState }
)(Dashboard);