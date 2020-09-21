import React, { Component } from "react";
import { Link } from "react-router-dom";
import PropTypes from "prop-types";
import { connect } from "react-redux";
import { logout } from "../../actions/securityActions";




import io from "socket.io-client";
import { QUIZ_PLAYING_STARTED, QUIZ_NEW_QUESTION_IN_PROGRESS, USER_FROM_YOUR_TEAM_ENTERED_ANSWER } from "../../actions/types";
import { setStateOfCurrentPhase, 
  setAnswerSelectedByUserFromYourTeam } from "../../actions/phasesActions";
import { getGames, setCurrentGame, getCurrentGame, setGameState } from "../../actions/gamesActions";
import { getMessages, afterPostMessage } from "../../actions/chatActions";

import {withRouter} from 'react-router'


class Header extends Component {
  
  constructor() {
    super();
    this.state = {
      isConnectionEstablished: false
    };
  }


  logout() {
    this.props.logout();
    window.location.href = "/";
  }

  componentDidMount() {
    this.props.getGames();
  //  this.socketInitialization();
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

    this.setState({
      isConnectionEstablished: true
    });
  }







  render() {
    const { validToken, user } = this.props.security;

    if (validToken && !this.state.isConnectionEstablished) {
      this.socketInitialization();
    }

    const userIsAuthenticated = (
      <div className="collapse navbar-collapse" >
        <ul className="navbar-nav ml-auto">
          <li className="nav-item">
            <Link className="nav-link" to="/dashboard">
              Games
            </Link>
          </li>
          <li className="nav-item">
            <Link className="nav-link" to="/profile">
              Profile ({user.email})
            </Link>
          </li>
          <li className="nav-item">
            <Link className="nav-link" to="/gamechat">
              Chat
            </Link>
          </li>
          <li className="nav-item">
            <Link
              className="nav-link"
              to="/logout"
              onClick={this.logout.bind(this)}
            >
              Logout
            </Link>
          </li>
        </ul>
      </div>
    );

    const userIsNotAuthenticated = (
      <div className="collapse navbar-collapse" id="mobile-nav">
        <ul className="navbar-nav ml-auto">
          <li className="nav-item">
            <Link className="nav-link" to="/register">
              Sign Up
            </Link>
          </li>
          <li className="nav-item">
            <Link className="nav-link" to="/login">
              Login
            </Link>
          </li>
        </ul>
      </div>
    );

    let headerLinks;

    if (validToken && user) {
    //if (true) {
      headerLinks = userIsAuthenticated;
    } else {
      headerLinks = userIsNotAuthenticated;
    }

    return (
      <nav className="navbar navbar-expand-sm navbar-dark bg-primary mb-4">
        <div className="container">
          {headerLinks}
        </div>
      </nav>
    );
  }
}

Header.propTypes = {
  logout: PropTypes.func.isRequired,
  security: PropTypes.object.isRequired
};

const mapStateToProps = state => ({
  security: state.security,
  games: state.games
});

/*
export default connect(
  mapStateToProps,
  { logout }
)(Header);
*/

/*
export default connect(
  mapStateToProps,
  { logout,
    getGames, 
    setCurrentGame, 
    getMessages,
    getCurrentGame, 
    setGameState, 
    setStateOfCurrentPhase,
    setAnswerSelectedByUserFromYourTeam
   }
)(Header);
*/


export default withRouter(connect(
  mapStateToProps,
  { logout,
    getGames, 
    setCurrentGame, 
    getMessages,
    getCurrentGame, 
    setGameState, 
    setStateOfCurrentPhase,
    setAnswerSelectedByUserFromYourTeam
   }
)(Header));
