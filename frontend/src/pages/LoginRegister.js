import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import Swal from "sweetalert2";
import {
  MDBContainer,
  MDBTabs,
  MDBTabsItem,
  MDBTabsLink,
  MDBTabsContent,
  MDBTabsPane,
  MDBBtn,
  MDBInput,
  MDBCheckbox
}
from 'mdb-react-ui-kit';
import authService from '../services/authService';

function LoginRegister() {

  const [justifyActive, setJustifyActive] = useState('tab1');;
  const [username, setUsername] = useState('');;
  const [password, setPassword] = useState('');;
  const [motPasse, setMotPasse] = useState('');;
  const [email, setEmail] = useState('');;
  const [name, setName] = useState('');;

  const handleJustifyClick = (value) => {
    if (value === justifyActive) {
      return;
    }

    setJustifyActive(value);
  };


  const history = useNavigate();

  const onSignIn = () => {
      FormData = {
        "username": username,
        "password": motPasse
      }
      let reponse = authService.login(FormData);
      if (reponse) {
        return history('/cours');
      }
  }

  const onSignUp = () => {
    FormData = {
      "name": name,
      "password": password,
      "username": email,
      "role" : 'ROLE_USER'
    }
    console.log("form", FormData);
    authService.register(FormData)
      .then(function (response) {
        Swal.fire({
          icon: "success",
          title: "User has been added successfully!",
          showConfirmButton: true,
        });
        console.log("dhh",response)
      })
      .catch(function (error) {
        Swal.fire({
          icon: "error",
          title: "Oops, Something went wrong!",
          showConfirmButton: true,
        });
      });
  }

  return (
    <MDBContainer className="p-3 my-5 d-flex flex-column w-50">

      <MDBTabs pills justify className='mb-3 d-flex flex-row justify-content-between'>
        <MDBTabsItem>
          <MDBTabsLink onClick={() => handleJustifyClick('tab1')} active={justifyActive === 'tab1'}>
            Login
          </MDBTabsLink>
        </MDBTabsItem>
        <MDBTabsItem>
          <MDBTabsLink onClick={() => handleJustifyClick('tab2')} active={justifyActive === 'tab2'}>
            Register
          </MDBTabsLink>
        </MDBTabsItem>
      </MDBTabs>

      <MDBTabsContent>

        <MDBTabsPane show={justifyActive === 'tab1'}>

          <MDBInput wrapperClass='mb-4' label='Email address' id='form1' name="username" onChange={(event) => {setUsername(event.target.value);}} value={username} type='email'/>
          <MDBInput wrapperClass='mb-4' label='Password' id='form2' name='password' onChange={(event) => {setMotPasse(event.target.value);}} value={motPasse} type='password'/>

          <div className="d-flex justify-content-between mx-4 mb-4">
            <MDBCheckbox name='flexCheck' value='' id='flexCheckDefault' label='Remember me' />
            <a href="!#">Forgot password?</a>
          </div>

          <MDBBtn onClick={onSignIn} className="mb-4 w-100">Sign in</MDBBtn>
          <p className="text-center">Not a member? <a href="#!">Register</a></p>

        </MDBTabsPane>

        <MDBTabsPane show={justifyActive === 'tab2'}>

          <MDBInput wrapperClass='mb-4' label='Name' id='form1' name='name' onChange={(event) => {setName(event.target.value);}} value={name} type='text'/>
          <MDBInput wrapperClass='mb-4' label='Email' id='form1' name='email' onChange={(event) => {setEmail(event.target.value);}} value={email} type='email'/>
          <MDBInput wrapperClass='mb-4' label='Password' id='form1' name='password' onChange={(event) => {setPassword(event.target.value);}} value={password} type='password'/>

          <div className='d-flex justify-content-center mb-4'>
            <MDBCheckbox name='flexCheck' id='flexCheckDefault' required label='I have read and agree to the terms' />
          </div>

          <MDBBtn onClick={onSignUp} className="mb-4 w-100">Sign up</MDBBtn>

        </MDBTabsPane>

      </MDBTabsContent>

    </MDBContainer>
  );
}

export default LoginRegister;