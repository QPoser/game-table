import React, { Component } from "react";

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