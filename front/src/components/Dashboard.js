import React, { Component } from "react";
//import ProjectItem from "./Project/ProjectItem";
//import CreateProjectButton from "./Project/CreateProjectButton";
import { connect } from "react-redux";
import { getGames, setCurrentGame } from "../actions/gamesActions";
import { getMessages, afterPostMessage } from "../actions/chatActions"
import PropTypes from "prop-types";
import Spinner from "./Spinner";
import Game from "./Game";
import { Link } from 'react-router-dom';
import io from "socket.io-client";

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
      /*
        var msgBody = JSON.parse(data);
        console.log('notification');
        console.log(msgBody);
        if (msgBody.Template === 'game_created') {
            alert('Game was created ' + msgBody.JsonValues.game)
        }
        if (msgBody.Template === 'game_started') {
            alert('Game was started ' + msgBody.JsonValues.game)
        }
        */
       debugger
    });
    socket.on('game_action', function (data) {
        /*
        var msgBody = JSON.parse(data);
        console.log('game_action');
        console.log(msgBody);
        if (msgBody.Template === 'game_started') {
            console.log('Game was started ' + msgBody.Game.Id)
        }
        */
       let msgBody = JSON.parse(data);
       if (msgBody.Template === 'your_game_started') {
        console.log('Game was started ' + msgBody.Game.Id);
        //this.props.setCurrentGame(msgBody.Game);
        this.props.getMessages(msgBody.Game.Id);
        this.props.history.push("/gamechat");
       }
       this.props.getGames();
       debugger
    }.bind(this));
    socket.on('chat', function (data) {
      /*
        var msgBody = JSON.parse(data);
        console.log('game_action');
        console.log(msgBody);
        if (msgBody.Template === 'game_started') {
            console.log('Game was started ' + msgBody.Game.Id)
        }
        */
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
  { getGames, setCurrentGame, getMessages }
)(Dashboard);