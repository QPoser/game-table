import React, { Component } from "react";
//import ProjectItem from "./Project/ProjectItem";
//import CreateProjectButton from "./Project/CreateProjectButton";
import { connect } from "react-redux";
import { getRooms } from "../actions/roomsActions";
import PropTypes from "prop-types";
import Spinner from "./Spinner";
import Room from "./Room";
import { Link } from 'react-router-dom';

class Dashboard extends Component {
  componentDidMount() {
    this.props.getRooms();
  }

  render() {
    const { data = [] } = this.props.rooms.rooms;

    

    const content = (
      <div className="projects">
      <div className="container">
        <div className="row">
          <div className="col-md-12">
            <h1 className="display-4 text-center">Rooms</h1>
            <br />
              <Link to="/addroom">
                  <span className="btn btn-primary">Create room</span>
              </Link>
            <br />
            <hr />
            <div className="row lead mb-4">
                <div className="col-md-2">
                  #
                </div>
                <div className="col-md-6">
                  Room
                </div>
                <div className="col-md-2">
                  Players
                </div>
                <div className="col-md-2">
                  Actions
                </div>
            </div>
            {data.map(room => (
              <Room key={room.id} room={room} />
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
  rooms: PropTypes.object.isRequired,
  getRooms: PropTypes.func.isRequired
};

const mapStateToProps = state => ({
  rooms: state.rooms
});

export default connect(
  mapStateToProps,
  { getRooms }
)(Dashboard);