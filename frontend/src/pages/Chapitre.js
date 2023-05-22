import React, { useEffect, useState } from 'react'
import Row from 'react-bootstrap/esm/Row';
import { useNavigate, useParams } from 'react-router-dom';
import ProjetService from '../services/projetService';
import Swal from 'sweetalert2';


function Chapitre() {
    const [chapitre, setChapitre] = useState([]);
    // eslint-disable-next-line react-hooks/rules-of-hooks
    const cours = useParams().id;
    console.log("id",cours);
    const projetService = new ProjetService();
    const navigate = useNavigate();

    useEffect(() => {
        projetService.getChapitreCours(cours).then(function (response) {
            setChapitre(response.data)
            console.log("chapitre",response.data)
        }).catch(function (error) {
            Swal.fire({
            icon: "error",
            title: "Oops, Something went wrong!",
            showConfirmButton: true,
            });
            navigate('/cours');
        });
    
    }, [cours])

    return (

        <Row className="justify-content-center">
            {chapitre.map((chap) => (
            <div className='mt-3'key={chap.id} style={{padding: '28px 17px', border: '#FF416C solid', background: 'none no-repeat scroll 15px 50%', width: '60%', margin: '0 auto', borderRadius: '12px' ,textAlign: 'center'}}>
                {chap.name}
            </div>
            ))}
        </Row>
        
    );
    
}
export default Chapitre;