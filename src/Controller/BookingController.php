<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\Booking;
use App\Form\BookingType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class BookingController extends Controller
{
    /**
     * @Route("/ads/{slug}/book", name="booking_create")
     * @IsGranted("ROLE_USER")
     */
    public function book(Ad $ad, Request $request, ObjectManager $manager)
    {
        $booking = new Booking();
        $form = $this->createForm(BookingType::class, $booking);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();

            $booking->setBooker($user)
                    ->setAd($ad);

            // Si les dates ne sont pas disponibles, message d'erreur
            if(!$booking->isBookableDates()) {
                $this->addFlash(
                    'warning',
                    "Les dates que vous avez choisi ne peuvent être réservées : elles sont déjà prises."
                );
            } else {
                // Sinon enregistrement et redirection
                $manager->persist($booking);
                $manager->flush();
    
                return $this->redirectToRoute('booking_show', ['id' => $booking->getId(), 'withAlert' => true]);
            }
        }

        return $this->render('booking/book.html.twig', [
            'ad' => $ad,
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet d'afficher la page d'une réservation
     *
     * @Route("/booking/{id}", name="booking_show")
     * 
     * @param Booking $booking
     * @return Response
     */
    public function show(Booking $booking) {
        return $this->render('booking/show.html.twig', [
            'booking' => $booking
        ]);
    }
    
}
