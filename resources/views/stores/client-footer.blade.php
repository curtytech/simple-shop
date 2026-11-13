   <footer class="bg-gray-800 text-white mt-16 bottom-0 left-0 right-0">
       <div class="container mx-auto px-4 py-8">
           <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
               <!-- Informações da Loja -->
               <div>
                   <h3 class="text-lg font-semibold mb-4">{{ $store->name }}</h3>
                   @if($store->slogan)
                   <p class="text-gray-300 mb-2">{{ $store->slogan }}</p>
                   @endif
                   @if($store->celphone)
                   <p class="text-gray-300">📞 {{ $store->celphone }}</p>
                   @endif
               </div>

               <!-- Links Rápidos -->
               <div>
                   <h3 class="text-lg font-semibold mb-4">Links Rápidos</h3>
                   <ul class="space-y-2 text-gray-300">
                       <li><a href="#" class="hover:text-white">Sobre Nós</a></li>
                       <li><a href="#" class="hover:text-white">Contato</a></li>
                       <li><a href="{{ route('store.privacy-policy', $store->slug) }}" class="hover:text-white">Política de Privacidade</a></li>
                       <li><a href="{{ route('store.terms', $store->slug) }}" class="hover:text-white">Termos de Uso</a></li>
                   </ul>
               </div>

               <!-- Redes Sociais -->
               <div>
                   <h3 class="text-lg font-semibold mb-4">Siga-nos</h3>
                   <div class="flex space-x-4">
                       @if($store->facebook)
                       <a href="https://facebook.com/{{ $store->facebook }}" target="_blank" class="text-gray-300 hover:text-white">
                           <i class="fa-brands fa-facebook"></i>
                       </a>
                       @endif
                       @if($store->instagram)
                       <a href="https://instagram.com/{{ $store->instagram }}" target="_blank" class="text-gray-300 hover:text-white">
                           <i class="fa-brands fa-instagram"></i>
                       </a>
                       @endif
                       @if($store->site)
                       <a href="{{ $store->site }}" target="_blank" class="text-gray-300 hover:text-white">
                            <i class="fa-solid fa-globe"></i>
                       </a>
                       @endif
                   </div>
               </div>
           </div>
       </div>
   </footer>