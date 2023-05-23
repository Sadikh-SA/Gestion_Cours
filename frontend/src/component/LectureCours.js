import React from 'react'
import { Document, Page } from 'react-pdf';

function LectureCours() {
    const [numPages, setNumPages] = React.useState(null);
    
    const handlePdfError = (error) => {
        console.error('Error while loading PDF:', error);
    };

    const pdfUrl = 'chemin/vers/votre/pdf.pdf'; // Remplacez par l'URL de votre fichier PDF

    return (
        <div>
            <Document
                file={pdfUrl}
                onLoadSuccess={({ numPages }) => setNumPages(numPages)}
                onLoadError={handlePdfError}
            >
                {Array.from(new Array(numPages), (el, index) => (
                <Page key={`page_${index + 1}`} pageNumber={index + 1} />
                ))}
            </Document>
        </div>
    );
}

export default LectureCours;