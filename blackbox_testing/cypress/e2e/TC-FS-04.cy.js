describe("TC-FS-04", () => {
    it("TC-FS-04", () => {
        cy.login();
        cy.viewport(1089, 825);
        cy.visit("/apps/files/files");

        cy.get(".files-fileList", { timeout: 10000 }).should("be.visible");
        cy.get(".action-share").first().click();

        cy.contains("button", "Share with user").click();

        cy.get('input[placeholder*="Name or email"]').type("fakeuser999");
        cy.contains("No results").should("be.visible");
        cy.contains("button", "Share").click();
        cy.contains("User not found").or(".error-message").should("be.visible");
    });
});