import React, { Component } from "react";
import { connect } from "react-redux";
import { join } from "../actions/gamesActions";

class Team extends Component {
  
  constructor() {
    super();
    this.state = {
      password: "",
      errors: {}
    };
    this.onChange = this.onChange.bind(this);
    //this.onSubmit = this.onSubmit.bind(this);
  }
  
  componentDidMount() {
  }

  /*
  onSubmit(e) {
    e.preventDefault();
    const JoinRequest = {
      id: this.props.game.id,
      password: this.state.password
    };

    this.props.join(JoinRequest);
  }
  */

  onChange(e) {
    this.setState({ [e.target.name]: e.target.value });
  }

  render() {

    

    const { id, title, slots, players, userInTeam } = this.props.team;

   

    return (
      
 <div className="row my-2">
     <div className="col-md-2">
        
      </div>
      <div className="col-md-2">
        {id}
      </div>
      <div className="col-md-4">
        {title}
      </div>
      <div className="col-md-2">
        {players.length + "/" +  slots}
      </div>
      <div className="col-md-2">
        <button className="btn btn-primary" onClick={()=>{this.props.onJoin(id)}}>Join</button>
    { userInTeam && <button className="btn btn-warning ml-2" onClick={()=>{this.props.onLeave()}}>Leave</button> } 
      </div>
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
)(Team);