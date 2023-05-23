import { useEffect, useState } from 'react';
import { Document, Page } from 'react-pdf/dist/esm/entry.webpack';
import { useNavigate, useParams } from 'react-router-dom';
import ProjetService from '../services/projetService';
import Swal from 'sweetalert2';

const ViewChapiter = () => {

    // eslint-disable-next-line react-hooks/rules-of-hooks
    const chapitreId = useParams().id;
    const [chapitre, setChapitre] = useState(0);
    const projetService = new ProjetService();
    const navigate = useNavigate();
    const fileUrl = 'http://localhost:8000/fichier/';
    useEffect(() => {
        projetService.getChapitre(chapitreId).then(function (response) {
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
    
    }, [chapitreId])
	const [numPages, setNumPages] = useState(null);
	const [pageNumber, setPageNumber] = useState(1);

	const onDocumentLoadSuccess = ({ numPages }) => {
		setNumPages(numPages);
	};

	const goToPrevPage = () =>
		setPageNumber(pageNumber - 1 <= 1 ? 1 : pageNumber - 1);

	const goToNextPage = () =>
		setPageNumber(
			pageNumber + 1 >= numPages ? numPages : pageNumber + 1,
		);

	return (
		<div>
			<nav>
				<button onClick={goToPrevPage}>Prev</button>
				<button onClick={goToNextPage}>Next</button>
				<p>
					Page {pageNumber} of {numPages}
				</p>
			</nav>

			<Document
				file={fileUrl+chapitre.fichier}
				onLoadSuccess={onDocumentLoadSuccess}
			>
				<Page pageNumber={pageNumber} />
			</Document>
		</div>
	);
};

export default ViewChapiter;