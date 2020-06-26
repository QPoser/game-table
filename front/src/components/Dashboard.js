import React, { Component } from "react";
//import ProjectItem from "./Project/ProjectItem";
//import CreateProjectButton from "./Project/CreateProjectButton";
import { connect } from "react-redux";
import { getGames } from "../actions/gamesActions";
import PropTypes from "prop-types";
import Spinner from "./Spinner";
import Game from "./Game";
import { Link } from 'react-router-dom';

class Dashboard extends Component {
  componentDidMount() {
    this.props.getGames();
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
  { getGames }
)(Dashboard);