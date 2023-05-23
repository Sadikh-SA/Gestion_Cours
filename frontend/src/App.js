import './App.css';
import { BrowserRouter as Router, Route, Switch, Routes } from 'react-router-dom';
import LoginRegister from './pages/LoginRegister';
import Cours from './pages/Cours';
import Chapitre from './pages/Chapitre';
import ViewChapiter from './pages/ViewChapitre';
//import Projet from './pages/Projet';

function App() {
  return (
    <div className="App">
      <div className="container mt-3">
        <Router>
          <Routes>
            <Route path="/" element={<LoginRegister />}/>
            <Route path="cours" element={<Cours />} />
            <Route path="chapitre/cours/:id" element={<Chapitre />} />
            <Route path="chapitre/:id" element={<ViewChapiter />} />
          </Routes>
        </Router>
      </div>
    </div>
  );
}

export default App;