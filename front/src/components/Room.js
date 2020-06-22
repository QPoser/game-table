import React, { Component } from "react";
import PropTypes from "prop-types";
import { connect } from "react-redux";
import classnames from "classnames";
import { join } from "../actions/roomsActions";

class Room extends Component {
  
  constructor() {
    super();
    this.state = {
      password: "",
      errors: {}
    };
    this.onChange = this.onChange.bind(this);
    this.onSubmit = this.onSubmit.bind(this);
  }
  
  componentDidMount() {
  }

  onSubmit(e) {
    e.preventDefault();
    const JoinRequest = {
      id: this.props.room.id,
      password: this.state.password
    };

    this.props.join(JoinRequest);
  }

  onChange(e) {
    this.setState({ [e.target.name]: e.target.value });
  }

  render() {

    

    const { id, title, slots, rules, secure  } = this.props.room;

    let actions;

      actions = 
      (<form onSubmit={this.onSubmit}>
        <div className="d-flex form-group">
            { secure 
            &&
            <input 
            type="password" 
            name="password" 
            className="form-control" 
            placeholder="password" 
            value={this.state.password}
            onChange={this.onChange}
            />
            }
            <button 
            className="btn btn-success input-group-append">Join</button>
        </div>
      </form>)


    const content = (
      <div className="row">
      <div className="col-md-2">
        {id}
      </div>
      <div className="col-md-6">
        {title}
      </div>
      <div className="col-md-2">
    <span className="badge badge-primary">{slots}</span>
      </div>
      <div className="col-md-2">
        {actions}
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



const mapStateToProps = state => ({
  security: state.security,
  errors: state.errors
});

export default connect(
  mapStateToProps,
  { join }
)(Room);