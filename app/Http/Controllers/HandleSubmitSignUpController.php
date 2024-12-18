<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProspectParent;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Http\Controllers\PaymentSpController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\WhatsAppController;
use App\Http\Controllers\inviteController;

class HandleSubmitSignUpController extends Controller
{
    protected $messageController;
    protected $whatsappController;
    protected $invoiceController;
    protected $inviteController;
    protected $prospectParent;

    public function __construct(
        MessageController $messageController,
        WhatsAppController $whatsappController,
        PaymentSpController $invoiceController,
        inviteController $inviteController,
        ProspectParent $prospectParent
    ) {
        $this->messageController = $messageController;
        $this->whatsappController = $whatsappController;
        $this->invoiceController = $invoiceController;
        $this->inviteController = $inviteController;
        $this->prospectParent = $prospectParent;
    }


    public function handleSignUp(Request $request)
    {
        try {
            // Validasi input
            $validated = $request->validate([
                'email' => 'required|email',
                'phone' => 'required|string|max:15'
            ]);
            // $invitationalCode = InvitonalCode::where('voucher_code', $validated['voucher_code'])->first();
            // Cek apakah data ada di tabel ParentProspect
            $parentEmailProspect = ProspectParent::where('email', $validated['email'])->first();

            $parentPhoneProspect = ProspectParent::where('phone', $validated['phone'])->first();



            if (!$parentEmailProspect || !$parentPhoneProspect) {
                return response()->json([
                    'message' => 'Belum Melakukan Pembayaran SP'
                ], 404);
            }

            // Cek apakah data sudah ada di tabel User
            $existingPhoneUser = User::where('phone', $validated['phone'])->first();
            $existingEmailUser = User::where('email', $validated['email'])>first();


            if ($existingEmailUser || $existingPhonelUser ) {

                return response()->json([
                    'message' => 'User already exists'
                ], 409);
            }

            $phone = $validated['phone'];
            $lastFourDigits = substr($phone, -4);
            
            
            // Masukkan data ke tabel User
            $user = User::create([
                'phone' => $validated['phone'],
                'email' => $validated['email'],
                'name' => ($parentProspect->name) ?? 'Anonymous', // Jika ada nama di ParentProspect, gunakan itu
                'password' => bcrypt($lastFourDigits)// Set default password, atau gunakan random password
            ]);

            return response()->json([
                'message' => 'User created successfully',
                'user' => $user
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function handleFormSubmission(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string',
            'email' => 'required|email',
            'source' => 'nullable|string|max:255',
            'invitional_code' => 'nullable|string|max:255',
            'id_city' => 'nullable|integer',
            'id_program' => 'required|integer',
        ]);

        try {
            $prospect = ProspectParent::create([
                'name' => $validated['name'],
                'phone' => $validated['phone'],
                'email' => $validated['email'],
                'source' => $validated['source'],
                'id_city' => $validated['id_city'],
                'id_program' => $validated['id_program'],
                'invitional_code' => $validated['invitional_code'] ?? null
            ]);

            $idParent = $prospect->id;
            $invoiceLink = '';

            if (empty($validated['invitional_code'])) {
            // if ($validated['invitional_code'] == "") {
                $invoiceRequest = new Request([
                    'id_parent' => $idParent,
                    'total' => 99000,
                    'payer_email' => $validated['email'],
                    'description' => "Pembayaran untuk SP untuk {$validated['name']}"
                ]);

                $invoiceResponse = $this->invoiceController->createInvoice($invoiceRequest);
                $invoiceLink = $invoiceResponse->getData()->link_pembayaran;
            }

            $messageId = !empty($validated['invitional_code']) ? 2 : 1;

            $response = $this->messageController->show($messageId);
            $messagesData = $response->getData()->message;

            if (!$messagesData || empty($messagesData)) {
                return response()->json(['error' => 'No messages found'], 500);
            }

            Log::info("User logged in". $invoiceLink);
            $messagesData = json_decode(json_encode($messagesData), true);
            $messageText = $messagesData['message'];
            $messageText = Str::replace([
                '{name1}',
                '{name2}',
                '{email}',
                '{payment_link}'
            ], [
                $validated['name'],
                $validated['name'],
                $validated['email'],
                $invoiceLink
            ], $messageText);

            $sendMessageRequest = new Request([
                'phone' => $validated['phone'],
                'message' => $messageText,
            ]);

            $sendMessageResponse = $this->whatsappController->sendMessage($sendMessageRequest);

            if (!$sendMessageResponse) {
                return response()->json(['error' => 'Failed to send WhatsApp message'], 500);
            }

            if (!empty($validated['invitional_code'])) {
            // if ($validated['invitional_code'] != "") {
                $codeRequest = new Request(['voucher_code' => $validated['invitional_code']]);
                $codeResult = $this->inviteController->checkInvitationalCode($codeRequest);
                $codeResultData = json_decode($codeResult->getContent(), true);

                if ($codeResultData['valid'] && $codeResultData['data']['id'] === 1 && $codeResultData['data']['status_voc'] === 1) {
                    return response()->json(['message' => 'Form submitted successfully', 'redirect' => '/thanks']);
                } else {
                    return response()->json(['error' => 'Invalid invitational code'], 400);
                }
            }

            return response()->json(['message' => 'Form submitted successfully', 'invoice_link' => $invoiceLink]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred', 'message' => $e->getMessage()], 500);
        }
    }
}
