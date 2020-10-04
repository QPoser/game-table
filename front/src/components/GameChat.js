import React, { Component } from 'react'
import { connect } from "react-redux";
import { getMessages, afterPostMessage, postMessage } from "../actions/chatActions"
import { getCurrentGame } from "../actions/gamesActions"
import { setCurrentPhase } from "../actions/phasesActions"
import { QUIZ_PLAYING_STARTED } from "../actions/types"
import Answers from "./Answers";
import AnswersPrice from "./AnswersPrice";
import { CountdownCircleTimer } from 'react-countdown-circle-timer'
import ReactDOM from 'react-dom';
import Countdown from 'react-countdown';

export class GameChat extends Component {


    constructor() {
        super();
        this.state = {
            messages: [{text:"bla bla bla"}, {text:"bla bla bla"}],
            chatmessage: "",
            recipient: "game",
            renderCountDown: false
        }
        this.handleChatMessageChange = this.handleChatMessageChange.bind(this);
        this.postMessage = this.postMessage.bind(this);
        this.changeMessageRecipient = this.changeMessageRecipient.bind(this);
        this.setCurrentPhaseHandle = this.setCurrentPhaseHandle.bind(this);
      }


    componentDidMount() {
        this.props.getCurrentGame();
    }

    componentWillReceiveProps(nextProps){
        /*
        if(this.props.question !== nextProps.question){
           this.setState({
            selectedOption: ""
           })
        }
        */
       
       /*
       this.setState({
        renderCountDown: false 
       })
       */ 
       this.setState({
        renderCountDown: true 
       })  
     }

    handleChatMessageChange(e) {
        this.setState({ [e.target.name]: e.target.value });
    }

    postMessage() {
       this.props.postMessage({gameId: this.props.games.game.data.id, content: this.state.chatmessage, recipient: this.state.recipient });
       this.setState({ chatmessage: "" });
    }

    changeMessageRecipient() {
        this.setState((state) => {
            const recipient = state.recipient === "game" ? "team" : "game";
            return { recipient };
          });
    }

    setCurrentPhaseHandle (phaseName){
        let gameId = this.props.games.game.data.id;
        this.props.setCurrentPhase(gameId, phaseName)
    };

    render() {

        
        const { data:messages=[] } = this.props.messages.messages;
        const { phases=[], selectedPhases=[], phaseInProgress={currentQuestion:{question:""}} } = this.props.phases;
        const { data:currentGame={teams:[]} } = this.props.games.game;
        const { gameState="" } = this.props.games;
        const { recipient } = this.state;
        const [leftTeam={title:"", players:[]}, rightTeam={title:"", players:[]}] = currentGame.teams;
        const { currentStepSeconds } = currentGame;
        

        const phasesMap =  phases.reduce(function(map, phase){
            if (!map[phase.type]) {
                map[phase.type] = [];
                map[phase.type].push(phase);
            } else {
                map[phase.type].push(phase);
            }
               return map; 
        }, {})



        const panelForChoosingPhases = (

            <React.Fragment>

            <h4 className="text-center">Phases to choose</h4>
            <div className="d-flex justify-content-around">
            {phases.map(phase => (
            <h4>
                {phase.type}
            </h4>                           
            ))}
            </div>


            <div className="d-flex justify-content-around mb-2 phases-container border border-info rounded">
            { Object.keys(phasesMap).map(key => (
                <div>
                    <ul className="list-group">
                        {phasesMap[key].map(phase => (
                            <li onClick={() => {
                                
                                this.setCurrentPhaseHandle(phase.name)
                            }
                                } className="list-group-item active">
                                {phase.name}
                            </li>
                        ))}
                    </ul>
                </div>
            ))
            }
            </div>
            </React.Fragment>
        )


        let mainPanel; 
         
        if (gameState != QUIZ_PLAYING_STARTED) {
            mainPanel = panelForChoosingPhases;
        } else {

           // selectedPhases


           try {
            mainPanel = <div className="mx-2 my-2 px-2 py-2 border border-info rounded">
            <div className="lead">{phaseInProgress.currentQuestion.question}</div>


            {phaseInProgress.type=="prices" ?
            <AnswersPrice question={phaseInProgress.currentQuestion.question}/>
            :
            <Answers answers={phaseInProgress.currentQuestion.answers}  question={phaseInProgress.currentQuestion.question}/>
            }

            </div>
           } catch (e) {
               
           }
        }

        const renderTime = ({ remainingTime }) => {
            if (remainingTime === 0) {
              return <div className="timer">Too lale...</div>;
            }
          
            return (
              <div className="timer">
                <div className="text">Remaining</div>
                <div className="value">{remainingTime}</div>
                <div className="text">seconds</div>
              </div>
            );
          };


          


          let countdownCircleTimer;
          
          //let renderCountDown = false;
      

          if (currentStepSeconds) {
           
           
           /* 
           let el = document.getElementById('timerwrapper');

           try {
           while (el && el.firstChild) {
               el.removeChild(el.firstChild);
           }
           } catch(e) {
               debugger
           }
           */

           /*
            countdownCircleTimer =  <CountdownCircleTimer
              isPlaying
              duration={currentStepSeconds}
              colors={[["#17A2B", 0.33], ["#17a2b8", 0.33], ["#28a745"]]}
              onComplete={() => {
                  
                this.setState({
                    renderCountDown: false 
                })  
                
                return [false, 100]}}
              size={180}
              >
              {renderTime}
              </CountdownCircleTimer>
            */
           
           countdownCircleTimer = <Countdown key={Date.now()} date={Date.now() + currentStepSeconds * 1000} />

          } else {
            countdownCircleTimer = <Countdown  date={Date.now()} />
          }



        return (
            
            <React.Fragment>

                <div className="row">    
                <div className="col-md-3">
                <h2 className="text-center">{leftTeam.title}</h2>
                <h2 className="text-center">points:{leftTeam.points}</h2>
                {leftTeam.players.map(player => (
                    <div className="ml-1 px-2 py-2 border border-info rounded d-flex justify-content-between">
                        {player.user.username}
                        {player.playerTurn && <span  className="badge badge-primary mx-2 d-flex align-items-center"> {"player turn".toUpperCase()}</span>} 
                        {player.user.vip && <span  className="badge badge-danger mr-2 d-flex align-items-center"> {"VIP".toUpperCase()}</span>} 
                    </div>
                ))}
                
                </div>
                <div className="col-md-6">  

                    <div className="timer-wrapper" id="timerwrapper">
                        {/*this.state.renderCountDown && countdownCircleTimer*/ countdownCircleTimer}
                    </div>


                    <h4 className="text-center">Selected phases</h4>
                    <div className="d-flex justify-content-around mb-2 px-2 py-2 border border-info rounded">
                        {selectedPhases.map(phase => (
                            <div className={`btn ${phase.id == phaseInProgress.id ? "btn-primary" : "btn-success"}`}>{phase.type}</div> 
                        ))}
                    </div>
                
                
                    {mainPanel}
                      
              
                <div className="scrollbar mb-4" id="style-1">
                {messages.map(message => (
                   <div className="card mx-4 my-4">
                       <div className="card-body p-1">
                       <h4><span  className="badge badge-primary my-2 d-inline-flex align-items-center"> {message.user.username.toUpperCase()}</span></h4>
                       <div className="p-2">
                           <p className="font-italic">{message.content}</p>
                       </div>
                       </div>
                   </div>
                ))}
                </div>
                <h4>Message will be sent to {recipient}</h4>
                    <textarea value={this.state.chatmessage} name="chatmessage" onChange={this.handleChatMessageChange} className="form-control py-3 px-3" placeholder="Write your message here..." rows="3"></textarea>
                    <div className="row">
                        <div className="col-md-8">
                            <button onClick={this.postMessage} className="btn btn-info btn-block mt-2">send message</button>
                        </div>    
                        <div className="col-md-4">
                            <div className="btn btn-primary btn-block mt-2" onClick={this.changeMessageRecipient} >Send message to {recipient === "game" ? "team" : "game"}</div>
                        </div>
                    </div>
                </div>    
                <div className="col-md-3">
                <h2 className="text-center">{rightTeam.title}</h2>
                {rightTeam.players.map(player => (
                    <div className="mr-1 px-2 py-2 border border-info rounded d-flex justify-content-between">
                          {player.user.username}
                          {player.playerTurn && <span  className="badge badge-primary mx-2 d-flex align-items-center"> {"player turn".toUpperCase()}</span>} 
                          {player.user.vip && <span  className="badge badge-danger mr-2 d-flex align-items-center"> {"VIP".toUpperCase()}</span>} 
                      </div>
                ))}
                </div>
                </div>
                
            </React.Fragment>
        )
    }
}

const mapStateToProps = state => {
    return {
        games: state.games,
        messages: state.messages,
        phases:state.phases
    }
}


export default connect(
    mapStateToProps, 
    {getMessages, postMessage, getCurrentGame, setCurrentPhase}
    )(GameChat);