import React, { Component } from "react";
import { connect } from "react-redux";


class Profile extends Component {
  
  constructor() {
    super();
  }
  
  componentDidMount() {
  }



  
  render() {

    

    const { email, roles } = this.props.security.user;

    return (
      
    <div className="container">
        <div className="row">
          <div className="col-md-12">
              <div className="lead text-left mt-4 mb-2">{email}</div>
          </div>
        </div>

        <div className="row">
          <div className="d-flex col-md-12">
            {roles.map(role => (
              <div className="badge badge-primary mr-2" > {role} </div>
            ))}
          </div>
        </div>

    </div>
       
    );
  }
}



const mapStateToProps = state => ({
  security: state.security
});

export default connect(
  mapStateToProps
)(Profile);