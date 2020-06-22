import React, { Component } from "react";
//import ProjectItem from "./Project/ProjectItem";
//import CreateProjectButton from "./Project/CreateProjectButton";
import { connect } from "react-redux";
import { getRooms } from "../actions/roomsActions";
import PropTypes from "prop-types";

class Spinner extends Component {


  render() {
      
    const content = (
      <div className="projects">
      <div className="container">
        <div className="row">
          <div className="col-md-12">
            <h1 className="display-4 text-center">Loading</h1>
          </div>
        </div>
      </div>
    </div>
    ); 

    return (
      <div>
        {content}
      </div>  
    );
  }
}


export default Spinner;