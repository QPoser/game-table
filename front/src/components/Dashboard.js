import React, { Component } from "react";
//import ProjectItem from "./Project/ProjectItem";
//import CreateProjectButton from "./Project/CreateProjectButton";
import { connect } from "react-redux";
import { getGames, setCurrentGame, getCurrentGame, setGameState } from "../actions/gamesActions";
import { getMessages, afterPostMessage } from "../actions/chatActions";
import { setStateOfCurrentPhase, 
  setAnswerSelectedByUserFromYourTeam } from "../actions/phasesActions";
import PropTypes from "prop-types";
import Spinner from "./Spinner";
import Game from "./Game";
import { Link } from 'react-router-dom';
import io from "socket.io-client";
import { QUIZ_PLAYING_STARTED, QUIZ_NEW_QUESTION_IN_PROGRESS, USER_FROM_YOUR_TEAM_ENTERED_ANSWER } from "../actions/types";

class Dashboard extends Component {
  
  constructor() {
    super();
  }

  componentDidMount() {
    this.props.getGames();
    //this.socketInitialization();
  }

  /*
  socketInitialization() {
    
    const jwtToken = localStorage.jwtToken;
    
    let socket = io('http://127.0.0.1:8888', {
        transports: ['websocket']
    });

    socket.emit('private', { // subscribe to private channel
        'token': jwtToken
    });
    
    socket.on('notifications', function (data) {
       
    });

    socket.on('game_action', function (data) {

       let msgBody = JSON.parse(data);
       console.log(msgBody.Template);
       if (msgBody.Template === 'your_game_started') {
        console.log('Game was started ' + msgBody.Game.Id);
        this.props.getMessages(msgBody.Game.Id);
        this.props.history.push("/gamechat");
       }
       this.props.getGames();
       

       
       


       

       if (msgBody.Template === 'quiz_game_finished'){
        this.props.history.push("/dashboard");
       }


       if (msgBody.Template === 'user_chose_phase_in_quiz'){
        this.props.getCurrentGame(this.props.history);
       }

       if (msgBody.Template === 'game_turns_changed'){
        this.props.getCurrentGame(this.props.history);
       }
       

       if (msgBody.Template === 'quiz_playing_started') {
         this.props.setGameState(QUIZ_PLAYING_STARTED)
       }

       if (msgBody.Template === USER_FROM_YOUR_TEAM_ENTERED_ANSWER) {

        this.props.setAnswerSelectedByUserFromYourTeam(msgBody.JsonValues.answer);

        this.props.setStateOfCurrentPhase(USER_FROM_YOUR_TEAM_ENTERED_ANSWER);
        
       }

       if (msgBody.Template === QUIZ_NEW_QUESTION_IN_PROGRESS) {
         this.props.setStateOfCurrentPhase(QUIZ_NEW_QUESTION_IN_PROGRESS);
       }


    }.bind(this));

    socket.on('chat', function (data) {
       
       var msgBody = JSON.parse(data);
       this.props.getMessages(msgBody.Game.Id);
       
    }.bind(this));

    socket.on('connect', (s) => {
      
    });
  }
  */


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
  { getGames, 
    setCurrentGame, 
    getMessages,
    getCurrentGame, 
    setGameState, 
    setStateOfCurrentPhase,
    setAnswerSelectedByUserFromYourTeam
   }
)(Dashboard);