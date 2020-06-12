import React, { Component } from "react";
//import ProjectItem from "./Project/ProjectItem";
//import CreateProjectButton from "./Project/CreateProjectButton";
import { connect } from "react-redux";
import { getRooms } from "../actions/roomsActions";
import PropTypes from "prop-types";

class Dashboard extends Component {
  componentDidMount() {
    this.props.getRooms();
  }

  render() {
    const { rooms } = this.props.rooms;

    return (
      <div className="projects">
        <div className="container">
          <div className="row">
            <div className="col-md-12">
              <h1 className="display-4 text-center">Projects</h1>
              <br />
              Create Room

              <br />
              <hr />
              {rooms.map(room => (
                room
              ))}
            </div>
          </div>
        </div>
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