@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto py-8">
    <div class="mb-6">
        <a href="{{ route('dashboard') }}" class="text-purple-600 hover:text-purple-700">
            <i class="ti ti-arrow-left"></i> Back to Dashboard
        </a>
    </div>
    
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-6 py-4">
            <h1 class="text-2xl font-bold text-white">Apply to become a {{ ucfirst($type) }}</h1>
            <p class="text-purple-200 text-sm">Submit your application and documents for review</p>
        </div>
        
        <form method="POST" action="{{ route('applications.store') }}" enctype="multipart/form-data" class="p-6">
            @csrf
            <input type="hidden" name="type" value="{{ $type }}">
            
            <div class="space-y-5">
                <!-- Personal Information Section -->
                <div>
                    <h3 class="font-semibold text-gray-800 mb-3">Personal Information</h3>
                    
                    <div class="grid md:grid-cols-2 gap-4">
                        <!-- Full Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Full Name <span class="text-red-500"></span>
                            </label>
                            <input type="text" name="full_name" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500" placeholder="Enter your full name" value="{{ auth()->user()->name ?? '' }}">
                        </div>
                        
                        <!-- Email -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Email Address <span class="text-red-500"></span>
                            </label>
                            <input type="email" name="email" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500" placeholder="Enter your email address" value="{{ auth()->user()->email ?? '' }}">
                        </div>
                        
                        <!-- Country -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Country <span class="text-red-500"></span>
                            </label>
                            <div class="relative">
                                <select name="country" id="country" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500 appearance-none bg-white pr-10">
                                    <option value="">Select your country</option>
                                    <option value="Afghanistan" data-code="+93">Afghanistan</option>
                                    <option value="Albania" data-code="+355">Albania</option>
                                    <option value="Algeria" data-code="+213">Algeria</option>
                                    <option value="Andorra" data-code="+376">Andorra</option>
                                    <option value="Angola" data-code="+244">Angola</option>
                                    <option value="Antigua and Barbuda" data-code="+1-268">Antigua and Barbuda</option>
                                    <option value="Argentina" data-code="+54">Argentina</option>
                                    <option value="Armenia" data-code="+374">Armenia</option>
                                    <option value="Australia" data-code="+61">Australia</option>
                                    <option value="Austria" data-code="+43">Austria</option>
                                    <option value="Azerbaijan" data-code="+994">Azerbaijan</option>
                                    <option value="Bahamas" data-code="+1-242">Bahamas</option>
                                    <option value="Bahrain" data-code="+973">Bahrain</option>
                                    <option value="Bangladesh" data-code="+880">Bangladesh</option>
                                    <option value="Barbados" data-code="+1-246">Barbados</option>
                                    <option value="Belarus" data-code="+375">Belarus</option>
                                    <option value="Belgium" data-code="+32">Belgium</option>
                                    <option value="Belize" data-code="+501">Belize</option>
                                    <option value="Benin" data-code="+229">Benin</option>
                                    <option value="Bhutan" data-code="+975">Bhutan</option>
                                    <option value="Bolivia" data-code="+591">Bolivia</option>
                                    <option value="Bosnia and Herzegovina" data-code="+387">Bosnia and Herzegovina</option>
                                    <option value="Botswana" data-code="+267">Botswana</option>
                                    <option value="Brazil" data-code="+55">Brazil</option>
                                    <option value="Brunei" data-code="+673">Brunei</option>
                                    <option value="Bulgaria" data-code="+359">Bulgaria</option>
                                    <option value="Burkina Faso" data-code="+226">Burkina Faso</option>
                                    <option value="Burundi" data-code="+257">Burundi</option>
                                    <option value="Cambodia" data-code="+855">Cambodia</option>
                                    <option value="Cameroon" data-code="+237">Cameroon</option>
                                    <option value="Canada" data-code="+1">Canada</option>
                                    <option value="Cape Verde" data-code="+238">Cape Verde</option>
                                    <option value="Central African Republic" data-code="+236">Central African Republic</option>
                                    <option value="Chad" data-code="+235">Chad</option>
                                    <option value="Chile" data-code="+56">Chile</option>
                                    <option value="China" data-code="+86">China</option>
                                    <option value="Colombia" data-code="+57">Colombia</option>
                                    <option value="Comoros" data-code="+269">Comoros</option>
                                    <option value="Congo" data-code="+242">Congo</option>
                                    <option value="Costa Rica" data-code="+506">Costa Rica</option>
                                    <option value="Côte d'Ivoire" data-code="+225">Côte d'Ivoire</option>
                                    <option value="Croatia" data-code="+385">Croatia</option>
                                    <option value="Cuba" data-code="+53">Cuba</option>
                                    <option value="Cyprus" data-code="+357">Cyprus</option>
                                    <option value="Czech Republic" data-code="+420">Czech Republic</option>
                                    <option value="Denmark" data-code="+45">Denmark</option>
                                    <option value="Djibouti" data-code="+253">Djibouti</option>
                                    <option value="Dominica" data-code="+1-767">Dominica</option>
                                    <option value="Dominican Republic" data-code="+1-809">Dominican Republic</option>
                                    <option value="Ecuador" data-code="+593">Ecuador</option>
                                    <option value="Egypt" data-code="+20">Egypt</option>
                                    <option value="El Salvador" data-code="+503">El Salvador</option>
                                    <option value="Equatorial Guinea" data-code="+240">Equatorial Guinea</option>
                                    <option value="Eritrea" data-code="+291">Eritrea</option>
                                    <option value="Estonia" data-code="+372">Estonia</option>
                                    <option value="Eswatini" data-code="+268">Eswatini</option>
                                    <option value="Ethiopia" data-code="+251">Ethiopia</option>
                                    <option value="Fiji" data-code="+679">Fiji</option>
                                    <option value="Finland" data-code="+358">Finland</option>
                                    <option value="France" data-code="+33">France</option>
                                    <option value="Gabon" data-code="+241">Gabon</option>
                                    <option value="Gambia" data-code="+220">Gambia</option>
                                    <option value="Georgia" data-code="+995">Georgia</option>
                                    <option value="Germany" data-code="+49">Germany</option>
                                    <option value="Ghana" data-code="+233">Ghana</option>
                                    <option value="Greece" data-code="+30">Greece</option>
                                    <option value="Grenada" data-code="+1-473">Grenada</option>
                                    <option value="Guatemala" data-code="+502">Guatemala</option>
                                    <option value="Guinea" data-code="+224">Guinea</option>
                                    <option value="Guinea-Bissau" data-code="+245">Guinea-Bissau</option>
                                    <option value="Guyana" data-code="+592">Guyana</option>
                                    <option value="Haiti" data-code="+509">Haiti</option>
                                    <option value="Honduras" data-code="+504">Honduras</option>
                                    <option value="Hungary" data-code="+36">Hungary</option>
                                    <option value="Iceland" data-code="+354">Iceland</option>
                                    <option value="India" data-code="+91">India</option>
                                    <option value="Indonesia" data-code="+62">Indonesia</option>
                                    <option value="Iran" data-code="+98">Iran</option>
                                    <option value="Iraq" data-code="+964">Iraq</option>
                                    <option value="Ireland" data-code="+353">Ireland</option>
                                    <option value="Israel" data-code="+972">Israel</option>
                                    <option value="Italy" data-code="+39">Italy</option>
                                    <option value="Jamaica" data-code="+1-876">Jamaica</option>
                                    <option value="Japan" data-code="+81">Japan</option>
                                    <option value="Jordan" data-code="+962">Jordan</option>
                                    <option value="Kazakhstan" data-code="+7">Kazakhstan</option>
                                    <option value="Kenya" data-code="+254">Kenya</option>
                                    <option value="Kiribati" data-code="+686">Kiribati</option>
                                    <option value="Kuwait" data-code="+965">Kuwait</option>
                                    <option value="Kyrgyzstan" data-code="+996">Kyrgyzstan</option>
                                    <option value="Laos" data-code="+856">Laos</option>
                                    <option value="Latvia" data-code="+371">Latvia</option>
                                    <option value="Lebanon" data-code="+961">Lebanon</option>
                                    <option value="Lesotho" data-code="+266">Lesotho</option>
                                    <option value="Liberia" data-code="+231">Liberia</option>
                                    <option value="Libya" data-code="+218">Libya</option>
                                    <option value="Liechtenstein" data-code="+423">Liechtenstein</option>
                                    <option value="Lithuania" data-code="+370">Lithuania</option>
                                    <option value="Luxembourg" data-code="+352">Luxembourg</option>
                                    <option value="Madagascar" data-code="+261">Madagascar</option>
                                    <option value="Malawi" data-code="+265">Malawi</option>
                                    <option value="Malaysia" data-code="+60">Malaysia</option>
                                    <option value="Maldives" data-code="+960">Maldives</option>
                                    <option value="Mali" data-code="+223">Mali</option>
                                    <option value="Malta" data-code="+356">Malta</option>
                                    <option value="Marshall Islands" data-code="+692">Marshall Islands</option>
                                    <option value="Mauritania" data-code="+222">Mauritania</option>
                                    <option value="Mauritius" data-code="+230">Mauritius</option>
                                    <option value="Mexico" data-code="+52">Mexico</option>
                                    <option value="Micronesia" data-code="+691">Micronesia</option>
                                    <option value="Moldova" data-code="+373">Moldova</option>
                                    <option value="Monaco" data-code="+377">Monaco</option>
                                    <option value="Mongolia" data-code="+976">Mongolia</option>
                                    <option value="Montenegro" data-code="+382">Montenegro</option>
                                    <option value="Morocco" data-code="+212">Morocco</option>
                                    <option value="Mozambique" data-code="+258">Mozambique</option>
                                    <option value="Myanmar" data-code="+95">Myanmar</option>
                                    <option value="Namibia" data-code="+264">Namibia</option>
                                    <option value="Nauru" data-code="+674">Nauru</option>
                                    <option value="Nepal" data-code="+977">Nepal</option>
                                    <option value="Netherlands" data-code="+31">Netherlands</option>
                                    <option value="New Zealand" data-code="+64">New Zealand</option>
                                    <option value="Nicaragua" data-code="+505">Nicaragua</option>
                                    <option value="Niger" data-code="+227">Niger</option>
                                    <option value="Nigeria" data-code="+234">Nigeria</option>
                                    <option value="North Korea" data-code="+850">North Korea</option>
                                    <option value="North Macedonia" data-code="+389">North Macedonia</option>
                                    <option value="Norway" data-code="+47">Norway</option>
                                    <option value="Oman" data-code="+968">Oman</option>
                                    <option value="Pakistan" data-code="+92">Pakistan</option>
                                    <option value="Palau" data-code="+680">Palau</option>
                                    <option value="Palestine" data-code="+970">Palestine</option>
                                    <option value="Panama" data-code="+507">Panama</option>
                                    <option value="Papua New Guinea" data-code="+675">Papua New Guinea</option>
                                    <option value="Paraguay" data-code="+595">Paraguay</option>
                                    <option value="Peru" data-code="+51">Peru</option>
                                    <option value="Philippines" data-code="+63">Philippines</option>
                                    <option value="Poland" data-code="+48">Poland</option>
                                    <option value="Portugal" data-code="+351">Portugal</option>
                                    <option value="Qatar" data-code="+974">Qatar</option>
                                    <option value="Romania" data-code="+40">Romania</option>
                                    <option value="Russia" data-code="+7">Russia</option>
                                    <option value="Rwanda" data-code="+250">Rwanda</option>
                                    <option value="Saint Kitts and Nevis" data-code="+1-869">Saint Kitts and Nevis</option>
                                    <option value="Saint Lucia" data-code="+1-758">Saint Lucia</option>
                                    <option value="Saint Vincent and the Grenadines" data-code="+1-784">Saint Vincent and the Grenadines</option>
                                    <option value="Samoa" data-code="+685">Samoa</option>
                                    <option value="San Marino" data-code="+378">San Marino</option>
                                    <option value="São Tomé and Príncipe" data-code="+239">São Tomé and Príncipe</option>
                                    <option value="Saudi Arabia" data-code="+966">Saudi Arabia</option>
                                    <option value="Senegal" data-code="+221">Senegal</option>
                                    <option value="Serbia" data-code="+381">Serbia</option>
                                    <option value="Seychelles" data-code="+248">Seychelles</option>
                                    <option value="Sierra Leone" data-code="+232">Sierra Leone</option>
                                    <option value="Singapore" data-code="+65">Singapore</option>
                                    <option value="Slovakia" data-code="+421">Slovakia</option>
                                    <option value="Slovenia" data-code="+386">Slovenia</option>
                                    <option value="Solomon Islands" data-code="+677">Solomon Islands</option>
                                    <option value="Somalia" data-code="+252">Somalia</option>
                                    <option value="South Africa" data-code="+27">South Africa</option>
                                    <option value="South Korea" data-code="+82">South Korea</option>
                                    <option value="South Sudan" data-code="+211">South Sudan</option>
                                    <option value="Spain" data-code="+34">Spain</option>
                                    <option value="Sri Lanka" data-code="+94">Sri Lanka</option>
                                    <option value="Sudan" data-code="+249">Sudan</option>
                                    <option value="Suriname" data-code="+597">Suriname</option>
                                    <option value="Sweden" data-code="+46">Sweden</option>
                                    <option value="Switzerland" data-code="+41">Switzerland</option>
                                    <option value="Syria" data-code="+963">Syria</option>
                                    <option value="Taiwan" data-code="+886">Taiwan</option>
                                    <option value="Tajikistan" data-code="+992">Tajikistan</option>
                                    <option value="Tanzania" data-code="+255" selected>Tanzania</option>
                                    <option value="Thailand" data-code="+66">Thailand</option>
                                    <option value="Timor-Leste" data-code="+670">Timor-Leste</option>
                                    <option value="Togo" data-code="+228">Togo</option>
                                    <option value="Tonga" data-code="+676">Tonga</option>
                                    <option value="Trinidad and Tobago" data-code="+1-868">Trinidad and Tobago</option>
                                    <option value="Tunisia" data-code="+216">Tunisia</option>
                                    <option value="Turkey" data-code="+90">Turkey</option>
                                    <option value="Turkmenistan" data-code="+993">Turkmenistan</option>
                                    <option value="Tuvalu" data-code="+688">Tuvalu</option>
                                    <option value="Uganda" data-code="+256">Uganda</option>
                                    <option value="Ukraine" data-code="+380">Ukraine</option>
                                    <option value="United Arab Emirates" data-code="+971">United Arab Emirates</option>
                                    <option value="United Kingdom" data-code="+44">United Kingdom</option>
                                    <option value="United States" data-code="+1">United States</option>
                                    <option value="Uruguay" data-code="+598">Uruguay</option>
                                    <option value="Uzbekistan" data-code="+998">Uzbekistan</option>
                                    <option value="Vanuatu" data-code="+678">Vanuatu</option>
                                    <option value="Vatican City" data-code="+379">Vatican City</option>
                                    <option value="Venezuela" data-code="+58">Venezuela</option>
                                    <option value="Vietnam" data-code="+84">Vietnam</option>
                                    <option value="Yemen" data-code="+967">Yemen</option>
                                    <option value="Zambia" data-code="+260">Zambia</option>
                                    <option value="Zimbabwe" data-code="+263">Zimbabwe</option>
                                </select>
                                <!-- Custom dropdown arrow -->
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Phone -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Phone Number <span class="text-red-500"></span>
                            </label>
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <input type="text" id="country_code" name="country_code" readonly class="w-24 px-3 py-2 border rounded-l-lg bg-gray-100 text-gray-700 font-medium" placeholder="+XXX" value="">
                                </div>
                                <input type="tel" name="phone" id="phone" required class="flex-1 px-4 py-2 border border-l-0 rounded-r-lg focus:ring-2 focus:ring-purple-500" placeholder="Enter phone number">
                            </div>
                            <p class="text-xs text-gray-400 mt-1">Country code will be automatically added</p>
                        </div>
                    </div>
                </div>

                <!-- Biography Section -->
                <div class="border-t pt-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Biography / About You <span class="text-red-500"></span>
                    </label>
                    <textarea name="biography" rows="5" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500" placeholder="Tell us about yourself, your experience, and motivation..."></textarea>
                    <p class="text-xs text-gray-400 mt-1">Provide a detailed description of your background and qualifications (Minimum 50 characters)</p>
                </div>
                
                <!-- Documents Section -->
                <div class="border-t pt-4">
                    <h3 class="font-semibold text-gray-800 mb-3">Required Documents</h3>
                    
                    <div class="space-y-4">
                        <!-- Passport Photo -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Passport Size Photo <span class="text-red-500"></span>
                            </label>
                            <input type="file" name="passport_photo" accept=".jpg,.jpeg,.png" required class="w-full">
                            <p class="text-xs text-gray-400 mt-1">JPG or PNG (Max 5MB) - This will be your profile identity photo</p>
                        </div>
                        
                        <!-- Supporting Document - Optional -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Supporting Document <span class="text-gray-400 text-xs">(Optional)</span>
                            </label>
                            <input type="file" name="supporting_document" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" class="w-full">
                            <p class="text-xs text-gray-400 mt-1">Any additional document that supports your application (PDF, JPG, PNG, DOC - Max 5MB)</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="flex gap-3 mt-8 pt-6 border-t">
                <button type="submit" class="flex-1 bg-gradient-to-r from-green-600 to-green-700 text-white px-6 py-3 rounded-lg hover:shadow-lg transition font-semibold">
                    Submit Application
                </button>
                <a href="{{ route('dashboard') }}" class="px-6 py-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition text-center">Cancel</a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const countrySelect = document.getElementById('country');
    const countryCodeInput = document.getElementById('country_code');
    const phoneInput = document.getElementById('phone');
    
    // Update country code when country changes
    countrySelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const code = selectedOption.getAttribute('data-code') || '';
        countryCodeInput.value = code;
        
        // Focus on phone input after selecting country
        if (code) {
            phoneInput.focus();
        }
    });
    
    // Trigger change event on page load if a country is pre-selected
    if (countrySelect.value) {
        countrySelect.dispatchEvent(new Event('change'));
    }
    
    // Prevent user from deleting country code
    countryCodeInput.addEventListener('keydown', function(e) {
        e.preventDefault();
    });
    
    // Combine country code + phone number when submitting
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const countryCode = countryCodeInput.value;
        const phone = phoneInput.value;
        
        // If phone doesn't start with country code, prepend it
        if (countryCode && phone && !phone.startsWith(countryCode)) {
            // Remove any existing + from phone
            const cleanPhone = phone.replace(/^\+/, '');
            phoneInput.value = countryCode + cleanPhone;
        }
    });
});
</script>
@endpush
@endsection