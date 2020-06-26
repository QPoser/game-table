import React, { Component } from "react";
import PropTypes from "prop-types";
import { connect } from "react-redux";
import classnames from "classnames";
import { join } from "../actions/gamesActions";
import Team from "./Team";

class Game extends Component {
  
  constructor() {
    super();
    this.state = {
      password: "",
      errors: {}
    };
    this.onChange = this.onChange.bind(this);
    this.onJoin = this.onJoin.bind(this);
  }
  
  componentDidMount() {
  }

  onJoin(team) {
    const JoinRequest = {
      id: this.props.game.id,
      password: this.state.password,
      team: team
    };

    this.props.join(JoinRequest);
  }

  onChange(e) {
    this.setState({ [e.target.name]: e.target.value });
  }

  render() {

    

    const { id, title, slots, rules, secure  } = this.props.game;

    let actions;

      actions = 
      (
        <div>
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
        </div>
      )


    const content = (
      <div>
    <div className="row bg-success text-white align-items-center py-2">
      <div className="col-md-2">
        {id}
      </div>
      <div className="col-md-6">
        {title}
      </div>
      <div className="col-md-4">
        {actions}
      </div>
  </div>
  
            <div className="row lead mb-2">
                <div className="col-md-2">
                  
                </div>
                <div className="col-md-2">
                  #
                </div>
                <div className="col-md-4">
                  Team
                </div>
                <div className="col-md-2">
                  Players
                </div>
                <div className="col-md-2">
                  Actions
                </div>
            </div>
  


  {this.props.game.teams.map(team => (
              <Team  key={team.id} team={team} onJoin={this.onJoin}  />
            ))}

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
)(Game);