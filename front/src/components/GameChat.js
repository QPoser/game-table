import React, { Component } from 'react'
import { Form, Input, Button, Row, Col, } from 'antd';
import Icon from '@ant-design/icons';
import io from "socket.io-client";
import { connect } from "react-redux";
//import moment from "moment";
import { getMessage, afterPostMessage } from "../actions/chatActions"
import ChatCard from "./ChatCard"
//import Dropzone from 'react-dropzone';
import Axios from 'axios';

export class GameChat extends Component {


    constructor() {
        super();
        this.state = {
            messages: [{text:"bla bla bla"}, {text:"bla bla bla"}],
            chatmessage: ""
        }
        this.handleChatMessageChange = this.handleChatMessageChange.bind(this);
        this.postMessage = this.postMessage.bind(this);
      }


    componentDidMount() {


        this.props.getMessage();



        //let server = "http://localhost:8888";

        /*
        this.props.getMessage();

        let socket = io("http://localhost:8888", {
            transports: ["websocket"]
        });

        socket.on("connect", function () {
            alert("connect");
        })
        */ 

        //this.socket = io(server);

        /*
        this.socket.on("Output Chat Message", messageFromBackEnd => {
            console.log(messageFromBackEnd)
            this.props.dispatch(afterPostMessage(messageFromBackEnd));
        })
        */
    }


    handleChatMessageChange(e) {
        this.setState({ [e.target.name]: e.target.value });
    }

    postMessage() {
        this.setState({ messages: [...this.state.messages, {text:this.state.chatmessage}] });
    }

    render() {

        const { messages } = this.state;

        return (
            
            <React.Fragment>
                <div className="container">
                <div className="scrollbar mb-4" id="style-1">
                {messages.map(message => (
                   <div className="card mx-4 my-4">
                       <div className="card-body p-1">
                       <h4 className="my-2">Dark thin</h4>
                       <div className="p-2">
                           <p className="font-italic">{message.text}</p>
                       </div>
                       </div>
                   </div>
                ))}
                </div>
                    <textarea value={this.state.chatmessage} name="chatmessage" onChange={this.handleChatMessageChange} className="form-control py-3 px-3" placeholder="Write your message here..." rows="3"></textarea>
                    <button onClick={this.postMessage} className="btn btn-info btn-block mt-2">send message</button>
                </div>    
            </React.Fragment>
        )
    }
}

const mapStateToProps = state => {
    return {
        user: state.user,
        chats: state.chat
    }
}


export default connect(
    mapStateToProps,
    { getMessage }
    )(GameChat);