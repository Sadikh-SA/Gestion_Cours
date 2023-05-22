import Button from 'react-bootstrap/Button';
import Card from 'react-bootstrap/Card';
import Swal from 'sweetalert2';
import { useEffect, useState } from 'react';
import ProjetService from '../services/projetService';

function Cours() {

    const [data, setData] = useState([]);
    const [currentPage, setCurrentPage] = useState(1);
    const [itemsPerPage] = useState(3);

    const projetService = new ProjetService(); 
    const imageUrl = 'http://localhost:8000/images/';

    useEffect(() => {
        projetService.getCours()
        .then(function (response) {
            console.log("dhh",response)
            setData(response.data)
        })
        .catch(function (error) {
            Swal.fire({
            icon: "error",
            title: "Oops, Something went wrong!",
            showConfirmButton: true,
            });
        });
    }, [])


    // Index de début et de fin des éléments à afficher sur la page actuelle
    const indexOfLastItem = currentPage * itemsPerPage;
    const indexOfFirstItem = indexOfLastItem - itemsPerPage;
    const currentItems = data.slice(indexOfFirstItem, indexOfLastItem);

    // Fonction pour changer de page
    const paginate = (pageNumber) => setCurrentPage(pageNumber);
    const handlePrevPage = () => {
        if (currentPage > 1) {
          setCurrentPage(currentPage - 1);
        }
      };
    
      const handleNextPage = () => {
        const totalPages = Math.ceil(data.length / itemsPerPage);
        if (currentPage < totalPages) {
          setCurrentPage(currentPage + 1);
        }
      };

    return (
        <div>
            <div className="row">
                {currentItems.map((item) => (
                <div className="col-md-4" key={item.id}>
                    <Card style={{ width: '23rem', marginTop: '1rem' }}>
                    <div className="card-image-wrapper">
                        <Card.Img src={imageUrl + item.images} className="card-image" />
                    </div>
                    <Card.Body>
                        <Card.Title>{item.nom}</Card.Title>
                        <Card.Text>{item.description}</Card.Text>
                        <Button variant="primary">Go somewhere</Button>
                    </Card.Body>
                    </Card>
                </div>
                ))}
            </div>
            <div className="pagination">
                <button 
                    onClick={handlePrevPage} 
                    disabled={currentPage === 1}
                >
                    Précédent
                </button>
                {Array.from({ length: Math.ceil(data.length / itemsPerPage) }).map((_, index) => (
                    <button
                        key={index}
                        onClick={() => paginate(index + 1)}
                        className={currentPage === index + 1 ? 'active' : ''}
                    >
                        {index + 1}
                    </button>
                ))}
                <button
                    onClick={handleNextPage}
                    disabled={currentPage === Math.ceil(data.length / itemsPerPage)}
                >
                    Suivant
                </button>
            </div>
        </div>
    );
}

export default Cours;