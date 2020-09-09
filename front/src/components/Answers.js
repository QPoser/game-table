import React, { Component } from "react";
import PropTypes from "prop-types";
import { connect } from "react-redux";
import classnames from "classnames";
import { join, leave } from "../actions/gamesActions";
import Team from "./Team";
import { postAnswer }  from "../actions/answersActions"

class Answers extends Component {
  
  constructor() {
    super();
    this.state = {
      selectedOption: "",
      errors: {}
    };
    /*
    this.onChange = this.onChange.bind(this);
    this.onJoin = this.onJoin.bind(this);
    this.onLeave = this.onLeave.bind(this);
    */
     this.handleOptionChange = this.handleOptionChange.bind(this);
     this.handleFormSubmit = this.handleFormSubmit.bind(this);
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

  onLeave() {
    const LeaveRequest = {
      id: this.props.game.id
    };

    this.props.leave(LeaveRequest);
  }

  onChange(e) {
    this.setState({ [e.target.name]: e.target.value });
  }

  
  handleOptionChange (e) {

    let so = ""
    if(this.state.selectedOption != e.target.value) {
        so = e.target.value;
    }

    this.setState({
      selectedOption: so
    });
  }

  handleFormSubmit (formSubmitEvent) {
    formSubmitEvent.preventDefault();

    console.log('You have selected:', this.state.selectedOption);

    const { data:currentGame={teams:[]} } = this.props.games.game;


    this.props.postAnswer(currentGame.id, this.state.selectedOption);

  }

  render() {

    
    const { answers  } = this.props;

    const content = answers.map(answer => (
        <div className="form-group">
        
          <input type="checkbox" id={answer.id} value={answer.id} checked={this.state.selectedOption == answer.id} onChange={this.handleOptionChange} />
          <label className="mx-2" for={answer.id}> {answer.id} </label>
       
      </div>

    ))


    return (
<div className="container my-2">
        <div className="row">
          <div className="col-sm-12">

            <form onSubmit={this.handleFormSubmit}>
              {content}
              <button className="btn btn-primary" type="submit">Save</button>
            </form>

          </div>
        </div>
      </div> 
    );
  }
}



const mapStateToProps = state => ({
  security: state.security,
  errors: state.errors,
  games: state.games
});

export default connect(
  mapStateToProps,
  { join, leave, postAnswer }
)(Answers);